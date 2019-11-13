#/bin/bash

DIR_WORK=$( cd $(dirname $0) ; pwd)
DIR_TMP=${DIR_WORK}/tmp
DIR_BASE_V1="../../../app/Api/v1"
DIR_BASE_V1_TESTS=${DIR_BASE_V1}"/Tests/Feature"
NAMESPACE_BASE_V1="App\\\Api\\\v1"
FILE_ROUTE_API="../../../routes/api.php"
CONTROLLER_TEMPLATE=DanteController.template
MODEL_TEMPLATE=DanteModel.template
ROUTE_TEMPLATE=DanteRoute.template
TEST_TEMPLATE=DanteTest.template

#
IN__DB_TABLE_NAME=
while getopts d:h:u:t: OPTION
do
	case ${OPTION} in
		d)	IN__DB_NAME=${OPTARG}
			;;
                h)      IN__DB_HOST=${OPTARG}
                        ;;
                u)      IN__DB_USER=${OPTARG}
                        ;;
		t)
			IN__DB_TABLE_NAME=${OPTARG}
			;;
	esac
done

#
if [[ ! -f ${CONTROLLER_TEMPLATE} ]]; then
        echo "Please, check \"CONTROLLER_TEMPLATE\" variable)."
        echo ""
        exit 1
fi

if [[ ! -f ${MODEL_TEMPLATE} ]]; then
        echo "Please, check \"MODEL_TEMPLATE\" variable."
        echo ""
        exit 1
fi

if [[ ! -f ${ROUTE_TEMPLATE} ]]; then
        echo "Please, check \"ROUTE_TEMPLATE\" variable."
        echo ""
        exit 1
fi

if [[ ! -f ${TEST_TEMPLATE} ]]; then
        echo "Please, check \"TEST_TEMPLATE\" variable."
        echo ""
        exit 1
fi

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
        #DB_TABLE_NAME=$( echo ${IN__DB_TABLE_NAME} | tr '[:upper:]' '[:lower:]' )
        DB_TABLE_NAME=${IN__DB_TABLE_NAME}
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
FILE_OUT_DB_FIELDS=${DIR_TMP}/${FILE_OUT_BASENAME}__DB_FIELDS.txt

#
QUERY_DESC="
SELECT c.COLUMN_NAME, c.COLUMN_TYPE, c.IS_NULLABLE, c.COLUMN_KEY, c.COLUMN_DEFAULT, c.COLUMN_COMMENT
FROM information_schema.tables t
JOIN information_schema.COLUMNS c ON c.table_schema=t.table_schema AND c.table_name=t.table_name
WHERE t.table_schema='${DB_NAME}' and t.table_name='${DB_TABLE_NAME}';
"

#
mysql -B -N -u${DB_USER} -p${DB_PASSWORD} -D${DB_NAME} -h${DB_HOST} -e"${QUERY_DESC}" 2> ${FILE_OUT_MYSQL_DESC_ERR} | tr \\t '|' > ${FILE_OUT_MYSQL_DESC}
mysql -B -N -u${DB_USER} -p${DB_PASSWORD} -D${DB_NAME} -h${DB_HOST} -e"SELECT * FROM ${DB_TABLE_NAME} ORDER BY id DESC LIMIT 1" 2> ${FILE_OUT_MYSQL_SELECT_ERR} | tr \\t '|' > ${FILE_OUT_MYSQL_SELECT}

# Removing 'id' and 'modified' (auto-generated fileds) from output
#cat ${FILE_OUT_MYSQL_DESC} | grep -v "^id|" > ${FILE_OUT_MYSQL_DESC}.tmp
#mv ${FILE_OUT_MYSQL_DESC}.tmp ${FILE_OUT_MYSQL_DESC}
#cat ${FILE_OUT_MYSQL_DESC} | grep -v "^modified|" > ${FILE_OUT_MYSQL_DESC}.tmp
#mv ${FILE_OUT_MYSQL_DESC}.tmp ${FILE_OUT_MYSQL_DESC}

# Preparing string for '$fillable' variable into Model and for '$inputParameters' variable into Test
if [ -s ${FILE_OUT_MYSQL_DESC} ]; then
	if [ -f ${FILE_OUT_DB_FIELDS} ]; then
		rm ${FILE_OUT_DB_FIELDS}
	fi

	FIELDS_COUNT=$( wc ${FILE_OUT_MYSQL_DESC} | awk '{print $1}' )
	FIELDS_N=1
	MODEL__FILLABLE__STRING=""
	TESTS__DATA__STRING=""
	TESTS__INPUTPARAMETERS__STRING=""
    TESTS__INPUTPARAMETERS_UPDATE__STRING=""
	while read LINE; do
		FIELD=$( echo ${LINE} | awk -F"|" '{print $1}' )

		if [[ "${FIELD}" == "id" ]] || [[ "${FIELD}" == "inserted" ]] || [[ "${FIELD}" == "modified" ]]; then
			echo "" > ${DIR_TMP}/null
		else
			MODEL__FILLABLE__STRING=$( echo -e "${MODEL__FILLABLE__STRING}\t\t'${FIELD}'\t\t=> 'string',@@@" )

                	if [ -s ${FILE_OUT_MYSQL_SELECT} ]; then
                        	FIELD_EXAMPLE=$( cat ${FILE_OUT_MYSQL_SELECT} | awk -F"|" '{print $"'"${FIELDS_N}"'"}' | sed 's/\//\\\//g' )
                	else
                        	FIELD_EXAMPLE=''
                	fi

                	# Set single quote for field that is not 'null'
	                if [[ "${FIELD_EXAMPLE}" == "NULL" ]] || [[ "${FIELD_EXAMPLE}" == "null" ]]; then
        	                FIELD_EXAMPLE='null'
                	else
                        	FIELD_EXAMPLE="'"${FIELD_EXAMPLE}"'"
	                fi
        	        TESTS__INPUTPARAMETERS__STRING="${TESTS__INPUTPARAMETERS__STRING}        '${FIELD}' => ${FIELD_EXAMPLE},@@@"

                    # Field for update
                    TESTS__INPUTPARAMETERS_UPDATE__STRING=$( echo -e "\t\t'${FIELD}' => ${FIELD_EXAMPLE}" )
		fi
		TESTS__DATA__STRING="${TESTS__DATA__STRING}        '${FIELD}',@@@"

		FIELDS_N=$(( ${FIELDS_N} + 1 ))
	done < ${FILE_OUT_MYSQL_DESC} 
else
	echo ""
        echo " The file \"${FILE_OUT_MYSQL_DESC}\" is empty."
        echo ""
        exit 1
fi
MODEL__FILLABLE__STRING=${MODEL__FILLABLE__STRING%????} # Remove last 4 chars (,@@@)
TESTS__INPUTPARAMETERS__STRING=${TESTS__INPUTPARAMETERS__STRING%????} # Remove last 4 chars (,@@@)
TESTS__DATA__STRING=${TESTS__DATA__STRING%????} # Remove last 4 chars (,@@@)

#
DB_TABLE_NAME__WITH_SPACE=$( echo ${DB_TABLE_NAME} | sed "s/_/ /g" | sed "s/-/ /g" )
DB_TABLE_NAME__CAMEL_CASE=
for WORD in ${DB_TABLE_NAME__WITH_SPACE}; do
	if (( $( echo ${DB_TABLE_NAME__WITH_SPACE} | wc -w | awk '{print $1}' ) > 1 )); then  
		WORD=$( echo ${WORD} | tr '[:upper:]' '[:lower:]' )
	fi
	DB_TABLE_NAME__CAMEL_CASE=${DB_TABLE_NAME__CAMEL_CASE}"$(tr '[:lower:]' '[:upper:]' <<< ${WORD:0:1})${WORD:1}"
done

#
DB_NAME_FIRST_LETTER_UPPER_CASE=$(tr '[:lower:]' '[:upper:]' <<< ${DB_NAME:0:1})${DB_NAME:1}

# Set CONTROLLER variables
DIR_CONTROLLERS=${DIR_BASE_V1}/Controllers/Tables
if [ ! -d ${DIR_CONTROLLERS} ]; then
	echo " The CONTROLLER directory \"${DIR_CONTROLLERS}\" doesn't exist; check it and try again"
	echo ""
	exit 1
fi
CREATE_CONTROLLER=0
CONTROLLER_NAME_CLASS="${DB_TABLE_NAME__CAMEL_CASE}Controller"
FILE_OUT_CONTROLLER=${DIR_CONTROLLERS}/${CONTROLLER_NAME_CLASS}.php
FILE_OUT_CONTROLLER_TMP=${DIR_TMP}/${CONTROLLER_NAME_CLASS}.php.tmp

# Set MODEL variables
DIR_MODELS=${DIR_BASE_V1}/Models/Tables
if [ ! -d ${DIR_MODELS} ]; then
        echo " The MODELS directory \"${DIR_MODELS}\" doesn't exist; check it and try again"
        echo ""
        exit 1
fi
CREATE_MODEL=0
MODEL_NAME_CLASS="${DB_TABLE_NAME__CAMEL_CASE}Model"
FILE_OUT_MODEL=${DIR_MODELS}/${MODEL_NAME_CLASS}.php
FILE_OUT_MODEL_TMP=${DIR_TMP}/${MODEL_NAME_CLASS}.php.tmp

# Set TEST variables
DIR_TESTS=${DIR_BASE_V1_TESTS}
if [ ! -d ${DIR_TESTS} ]; then
        echo " The TESTS directory \"${DIR_TESTS}\" doesn't exist; check it and try again"
        echo ""
        exit 1
fi
CREATE_TEST=0
TEST_NAME_CLASS="${DB_TABLE_NAME__CAMEL_CASE}ControllerTest"
FILE_OUT_TEST=${DIR_TESTS}/${TEST_NAME_CLASS}.php
FILE_OUT_TEST_TMP=${DIR_TMP}/${TEST_NAME_CLASS}.php.tmp

# Set ROUTE variables
CREATE_ROUTE=0
FILE_API_TMP=${DIR_TMP}/api.php.tmp


echo "-----"
#
if [ -f ${FILE_OUT_CONTROLLER} ]; then
	echo -n " The Controller file \"${FILE_OUT_CONTROLLER}\" already exists; substitute it? (Y/N)? "
	read ANSWER
	ANSWER2=$( echo ${ANSWER} | tr '[a-z]' '[A-Z]' )
	if ( [ "${ANSWER2}" == "Y" ] ); then
		CREATE_CONTROLLER=1
	fi
else
	CREATE_CONTROLLER=1
fi
if (( ${CREATE_CONTROLLER} == 1 )); then
	cat ${CONTROLLER_TEMPLATE} | sed "s/--ControllerNameClass--/${CONTROLLER_NAME_CLASS}/g" | sed "s/--ModelNameClass--/${MODEL_NAME_CLASS}/g" | sed "s/--db_table_name--/${DB_TABLE_NAME}/g" | sed "s/--BaseNamespace--/${NAMESPACE_BASE_V1}/g"> ${FILE_OUT_CONTROLLER_TMP}
	mv ${FILE_OUT_CONTROLLER_TMP} ${FILE_OUT_CONTROLLER}
	echo " The new Controller was created:"
	echo "  ${FILE_OUT_CONTROLLER}"
	echo "  remeber to set \"validator\" in the Controller"
	echo ""
fi
echo ""

#
if [ -f ${FILE_OUT_MODEL} ]; then
        echo -n " The Model file \"${FILE_OUT_MODEL}\" already exists; substitute it? (Y/N)? "
        read ANSWER
        ANSWER2=$( echo ${ANSWER} | tr '[a-z]' '[A-Z]' )
        if ( [ "${ANSWER2}" == "Y" ] ); then
		CREATE_MODEL=1
        fi
else
	CREATE_MODEL=1
fi
if (( ${CREATE_MODEL} )); then
	cat ${MODEL_TEMPLATE} | sed -e "s/--ModelNameClass--/${MODEL_NAME_CLASS}/g" -e "s/--db_table_name--/${DB_TABLE_NAME}/g" -e "s/--dbname--/${DB_NAME}/g" -e "s/--BaseNamespace--/${NAMESPACE_BASE_V1}/g" -e "s/--db_fillable_fields--/${MODEL__FILLABLE__STRING}/g" -e $'s/@@@/\\\n/g' > ${FILE_OUT_MODEL_TMP}
	mv ${FILE_OUT_MODEL_TMP} ${FILE_OUT_MODEL}
	echo " The new Model was created:"
	echo "  ${FILE_OUT_MODEL}"
	echo "  remember to check \"fillable\" in the Model"
	echo ""
fi
echo ""

#
if [ -f ${FILE_OUT_TEST} ]; then
        echo -n " The Test file \"${FILE_OUT_TEST}\" already exists; substitute it? (Y/N)? "
        read ANSWER
        ANSWER2=$( echo ${ANSWER} | tr '[a-z]' '[A-Z]' )
        if ( [ "${ANSWER2}" == "Y" ] ); then
                CREATE_TEST=1
        fi
else
        CREATE_TEST=1
fi
if (( ${CREATE_TEST} == 1 )); then
        cat ${TEST_TEMPLATE} | sed -e "s/--TestNameClass--/${TEST_NAME_CLASS}/g" -e "s/--uri--/\/api\/${DB_NAME}\/_table\/v1\/${DB_TABLE_NAME}/g" -e "s/--db_inputParameters_fields--/${TESTS__INPUTPARAMETERS__STRING}/g" -e "s/--db_inputParameters_update_fields--/${TESTS__INPUTPARAMETERS_UPDATE__STRING}/g" -e "s/--db_data_fields--/${TESTS__DATA__STRING}/g" -e $'s/@@@/\\\n/g' > ${FILE_OUT_TEST_TMP}
        mv ${FILE_OUT_TEST_TMP} ${FILE_OUT_TEST}
        echo " The new Test was created:"
        echo "  ${FILE_OUT_TEST}"
        echo "  remeber to check \"inputParameters\", \"inputParametersForUpdate\" and \"data\" variables"
        echo ""
fi
echo ""

# Build route file
if grep -q '\-\-PLACEHOLDER\-\-' ${FILE_ROUTE_API} ; then
	if grep -q "\'${DB_TABLE_NAME}\'" ${FILE_ROUTE_API} ; then
        	echo -n " The 'route' already exists into the file \"${FILE_ROUTE_API}\"; update it? (Y/N)? "
		read ANSWER
		ANSWER2=$( echo ${ANSWER} | tr '[a-z]' '[A-Z]' )
		if ( [ "${ANSWER2}" == "Y" ] ); then
			CREATE_ROUTE=1
		fi
	else
		CREATE_ROUTE=1
	fi
else
	echo " ATTENTION: I cannot find '--PLACEHOLDER--' comment in the \"${FILE_ROUTE_API}\"; please, set the 'route' manually"
	echo ""
	exit 1
fi
if (( ${CREATE_ROUTE} == 1 )); then
	# remove the route if it is presente
	grep -v "'${DB_TABLE_NAME}'" ${FILE_ROUTE_API} > ${FILE_API_TMP}

	# get the --PALCEHOLDER-- line number
	LINE_NUMBER_PLACEHOLDER=$( grep -n '\-\-PLACEHOLDER\-\-' ${FILE_API_TMP} | awk -F":" '{print $1}' )

	# get total line number
	TOTAL_LINES=$( wc -l ${FILE_API_TMP} | awk '{print $1}' )

	# get head line
	head -$(( ${LINE_NUMBER_PLACEHOLDER} - 1 )) ${FILE_API_TMP} > ${FILE_API_TMP}.head

	# get tail line
	tail -$(( ${TOTAL_LINES} - ${LINE_NUMBER_PLACEHOLDER} )) ${FILE_API_TMP} > ${FILE_API_TMP}.tail 
	
	# build new pice of route
	cat ${ROUTE_TEMPLATE} | sed "s/--ControllerNameClass--/${CONTROLLER_NAME_CLASS}/g" | sed "s/--db_table_name--/${DB_TABLE_NAME}/g" | sed "s/--BaseNamespace--/${NAMESPACE_BASE_V1}/g" | sed "s/--dbname--/${DB_NAME}/g"> ${FILE_API_TMP}.newroute
    #echo "        '"${DB_TABLE_NAME}"'         => '"App\Api\v1\Controllers\${CONTROLLER_NAME_CLASS}"'," > ${FILE_API_TMP}.newroute

	# build final route file
	cat ${FILE_API_TMP}.head ${FILE_API_TMP}.newroute ${FILE_API_TMP}.tail > ${FILE_API_TMP}

	# move new file
	mv ${FILE_API_TMP} ${FILE_ROUTE_API}

	#
	echo " The route file \"${FILE_ROUTE_API}\" was updated."
	echo ""
fi
echo ""
echo ""

#
if [ -d ${DIR_TMP} ]; then
	rm -fr ${DIR_TMP}
fi
