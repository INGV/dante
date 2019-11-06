#/bin/bash

DIR_WORK=$( cd $(dirname $0) ; pwd)
DIR_TMP=${DIR_WORK}/tmp
DIR_SWAGGER="../../../app/Api/V1/Swagger"

#
IN__DB_TABLE_NAME=
while getopts d:h:u:t: OPTION
do
	case ${OPTION} in
		d)	IN__DB_NAME=${OPTARG}
			;;
		h)	IN__DB_HOST=${OPTARG}
			;;
		u)	IN__DB_USER=${OPTARG}
			;;
		t)
			IN__DB_TABLE_NAME=${OPTARG}
			;;
	esac
done

#
if [[ -z ${IN__DB_NAME} ]]; then
        echo "Please, set DB_NAME variable (-d option)."
        echo ""
        exit 1
else
        DB_NAME=$( echo ${IN__DB_NAME} | tr '[:upper:]' '[:lower:]' )
fi

if [[ -z ${IN__DB_HOST} ]]; then
        echo "Please, set DB_HOST variable (-h option)."
        echo ""
        exit 1
else
        DB_HOST=$( echo ${IN__DB_HOST} | tr '[:upper:]' '[:lower:]' )
fi

if [[ -z ${IN__DB_USER} ]]; then
        echo "Please, set DB_USER variable (-u option)."
        echo ""
        exit 1
else
        DB_USER=$( echo ${IN__DB_USER} | tr '[:upper:]' '[:lower:]' )
fi

if [[ -z ${IN__DB_TABLE_NAME} ]]; then
        echo "Please, set DB_TABLE_NAME variable (-t option)."
        echo ""
        exit 1
else
        DB_TABLE_NAME=$( echo ${IN__DB_TABLE_NAME} | tr '[:upper:]' '[:lower:]' )
fi

#
if [ ! -d ${DIR_TMP} ]; then
	mkdir ${DIR_TMP}
fi

# Get MySQL password
echo -n " Write MySQL password: "
read DB_PASSWORD

# Set variables
FILE_OUT_BASENAME="${DB_HOST}__${DB_NAME}__${DB_USER}"
FILE_OUT_MYSQL_DESC=${DIR_TMP}/${FILE_OUT_BASENAME}__MYSQL_DESC.txt
FILE_OUT_MYSQL_DESC_ERR=${DIR_TMP}/${FILE_OUT_BASENAME}__MYSQL_DESC.err
FILE_OUT_MYSQL_SELECT=${DIR_TMP}/${FILE_OUT_BASENAME}__MYSQL_SELECT.txt
FILE_OUT_MYSQL_SELECT_ERR=${DIR_TMP}/${FILE_OUT_BASENAME}__MYSQL_SELECT.err
FILE_OUT_SWAGGER_DEFINITION=${DIR_TMP}/swagger_definition_${DB_TABLE_NAME}.txt
FILE_OUT_SWAGGER_PATH=${DIR_TMP}/swagger_path_${DB_TABLE_NAME}.txt

#
QUERY_DESC="
SELECT c.COLUMN_NAME, c.COLUMN_TYPE, c.IS_NULLABLE, c.COLUMN_KEY, c.COLUMN_DEFAULT, c.COLUMN_COMMENT 
FROM information_schema.tables t 
JOIN information_schema.COLUMNS c ON c.table_schema=t.table_schema AND c.table_name=t.table_name 
WHERE t.table_schema='${DB_NAME}' and t.table_name='${DB_TABLE_NAME}';
"

#mysql -B -N -u${DB_USER} -p${DB_PASSWORD} -D${DB_NAME} -h${DB_HOST} -e"DESC ${DB_TABLE_NAME}" 2> ${FILE_OUT_MYSQL_DESC_ERR} | tr \\t '|' > ${FILE_OUT_MYSQL_DESC}
mysql -B -N -u${DB_USER} -p${DB_PASSWORD} -D${DB_NAME} -h${DB_HOST} -e"${QUERY_DESC}" 2> ${FILE_OUT_MYSQL_DESC_ERR} | tr \\t '|' > ${FILE_OUT_MYSQL_DESC}
mysql -B -N -u${DB_USER} -p${DB_PASSWORD} -D${DB_NAME} -h${DB_HOST} -e"SELECT * FROM ${DB_TABLE_NAME} ORDER BY id DESC LIMIT 1" 2> ${FILE_OUT_MYSQL_SELECT_ERR} | tr \\t '|' > ${FILE_OUT_MYSQL_SELECT}
FILED_COUNT=$( wc -l ${FILE_OUT_MYSQL_DESC} | awk '{print $1}' )

FIELD_N=1
if [ -s ${FILE_OUT_MYSQL_DESC} ] && [ -s ${FILE_OUT_MYSQL_SELECT} ]; then
	echo "        \"${DB_TABLE_NAME}Base\": {"
	echo "            \"properties\": {"


	FIELDS_NOT_NULL=""
	while read LINE; do 
		FIELD=$( echo ${LINE} | awk -F"|" '{print $1}' )
		FIELD_TYPE=$( echo ${LINE} | awk -F"|" '{print $2}' )
        	FIELD_NULL=$( echo ${LINE} | awk -F"|" '{print $3}' )
        	FIELD_DEFAULT=$( echo ${LINE} | awk -F"|" '{print $5}' )
        	FIELD_COMMENT=$( echo ${LINE} | awk -F"|" '{print $6}' )
		FIELD_EXAMPLE=$( cat ${FILE_OUT_MYSQL_SELECT} | awk -F"|" '{print $"'"${FIELD_N}"'"}' )

	        # get fields that are NOT NULL, including 'id', 'modified' and 'inserted' that are auto-generated
        	if [[ "${FIELD_NULL}" == "NO" ]] && [[ "${FIELD}" != "id" ]] && [[ "${FIELD}" != "modified" ]] && [[ "${FIELD}" != "inserted" ]]; then
                FIELDS_NOT_NULL="${FIELDS_NOT_NULL} ${FIELD}"
        	fi

            # set comma
            if (( ${FILED_COUNT} == ${FIELD_N} )); then
                COMMA=""
            else
                COMMA=","
            fi

            # set DEFAULT
            if [[ "${FIELD_DEFAULT}" == "NULL" ]]; then
                DEFAULT=""
            else
                if [[ $FIELD_TYPE == *"int"* ]] || [[ $FIELD_TYPE == *"double"* ]] || [[ $FIELD_TYPE == *"boolean"* ]]; then
                    DEFAULT=",
                    \"default\": ${FIELD_DEFAULT}"
                else
                    DEFAULT=",
                    \"default\": \"${FIELD_DEFAULT}\""
                fi
            fi            

            # set EXAMPLE
            if [[ $FIELD_EXAMPLE == "NULL" ]]; then
                EXAMPLE=""
            else
                if [[ $FIELD_TYPE == *"int"* ]] || [[ $FIELD_TYPE == *"double"* ]] || [[ $FIELD_TYPE == *"boolean"* ]]; then
                    EXAMPLE=",
                    \"example\": ${FIELD_EXAMPLE}"
                else
                    EXAMPLE=",
                    \"example\": \"${FIELD_EXAMPLE}\""
                fi
            fi

	        # set SWAGGER_TYPE and FORMAT
            FORMAT=
        	if [[ $FIELD_TYPE == *"int"* ]]; then
            		SWAGGER_TYPE="integer"
                    FORMAT=",
                    \"format\": \"int64\""
        	elif [[ $FIELD_TYPE == *"double"* ]]; then
            		SWAGGER_TYPE="number"
                    FORMAT=",
                    \"format\": \"double\""
        	elif [[ $FIELD_TYPE == *"boolean"* ]]; then
            		SWAGGER_TYPE="boolean"
        	else
            		SWAGGER_TYPE="string"
        	fi

            # set EXAMPLE
            

cat << EOF
                "${FIELD}": {
                    "type": "${SWAGGER_TYPE}",
                    "description": "${FIELD_COMMENT} | ${FIELD_TYPE}"${FORMAT}${EXAMPLE}${DEFAULT}
                }${COMMA}
EOF
		FIELD_N=$(( ${FIELD_N} + 1 ))
	done < ${FILE_OUT_MYSQL_DESC}


	echo "            },"
    echo "            \"required\":["

    FIELDS_NOT_NULL_COUNT=$( echo ${FIELDS_NOT_NULL} | wc -w | awk '{print $1}' )
    FIELDS_NOT_NULL_N=1
    for FIELD_NOT_NULL in ${FIELDS_NOT_NULL}; do
        if (( ${FIELDS_NOT_NULL_N} == ${FIELDS_NOT_NULL_COUNT} )); then
            echo "                \"${FIELD_NOT_NULL}\""
        else
            echo "                \"${FIELD_NOT_NULL}\","
        fi
        FIELDS_NOT_NULL_N=$(( ${FIELDS_NOT_NULL_N} + 1 ))
    done

    echo "            ]"
	echo "        },"


cat << EOF
        "${DB_TABLE_NAME}": {
            "allOf": [
                {
                    "\$ref" : "#\/definitions\/${DB_TABLE_NAME}Base"
                },
                {
                    "properties": {
                        < !!! START-COMMENT !!!
                          spostare qui, prendendole da sopra, le proprieta' da non passare nel POST in quanto generate in automatico; es: id (auto_increment), modified (on update CURRENT_TIMESTAMP), ecc...
                        di default si puo anche pensare di inserire i seguenti 'riferimenti':
                        "id": {
                            "\$ref" : "#\/definitions\/id"
                        },
                        "modified": {
                            "\$ref" : "#\/definitions\/modified"
                        },
                        "inserted": {
                            "\$ref" : "#\/definitions\/inserted"
                        }
                        !!! END-COMMENT !!!>
                    }
                }
            ]
        },
        "${DB_TABLE_NAME}Post": {
            "\$ref" : "#\/definitions\/${DB_TABLE_NAME}Base"
        },
        "${DB_TABLE_NAME}Response": {
            "properties": {
                "meta": {
                    "\$ref" : "#\/definitions\/metaDefinition"
                },
                "data": {
                    "type" :"array",
                    "items": {
                        "\$ref" : "#\/definitions\/${DB_TABLE_NAME}"
                    }
                }
            }
        }
EOF
else
	echo " Error!"
    echo "  - the table could be empty!"
    echo "  - generic error, check the script! :-( "
fi


#
if [ -d ${DIR_TMP} ]; then
        rm -fr ${DIR_TMP}
fi
