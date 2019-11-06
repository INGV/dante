#!/bin/bash

BASENAME="`basename $0`"
DIRNAME=$( cd $(dirname $0) ; pwd)
FILE_OUTPUT="out.log"
COMMAND="curl -i -o ${DIRNAME}/${FILE_OUTPUT} --silent --write-out %{http_code} -X POST"
BIND_EWKEY_ROUTE="TYPE_QUAKE2K:quake2k TYPE_HYP2000ARC:hyp2000arc TYPE_MAGNITUDE:magnitude TYPE_PICK_SCNL:pick_scnl"

function syntax() {
cat << EOF
Syntax: ${BASENAME} -f <hew-dump-file> -b <webservices-host-and-port>
  Options:
	-f		Input earthwrom dump file
	-b		Web Service host (and port)

  Example:	${BASENAME} -f hew3.json.messages.dump -b "albus.int.ingv.it:9595"
  		${BASENAME} -f hew3.json.messages.dump -b "jabba.int.ingv.it:10014"

EOF
}
### START - Check parameters ###
IN__EW_INPUT_FILE=
IN__WS_HOST=
while getopts hf:b: OPTION
do
        case $OPTION in
                h)
                        syntax
                        exit 1
                        ;;
                f)      IN__EW_INPUT_FILE=${OPTARG}
                        ;;
                b)      IN__WS_HOST=${OPTARG}
                        ;;
                \?)
                        syntax
                        exit 1
                        ;;
        esac
done
### END - Check parameters ###

# Check input
if [ -z "${IN__EW_INPUT_FILE}" ]; then
	echo ""
	echo "ERROR: Give me an earthworm input file."
	syntax
	exit
else
	if [ -f ${IN__EW_INPUT_FILE} ]; then
		EW_INPUT_FILE=${IN__EW_INPUT_FILE}
	else
	        echo ""
        	echo "ERROR: The file \"${1}\" doesn't exist."
	        syntax
        	exit
	fi
fi

if [ -z ${IN__WS_HOST} ]; then
        echo ""
        echo "ERROR: Give me a host and port."
        syntax
        exit
else
	WS_HOST=${IN__WS_HOST}
fi

#
N_LINE=$( wc -l ${EW_INPUT_FILE} | awk '{print $1}' )
COUNT=1
while read LINE; 
do
	MESSAGE_TYPE=$( echo ${LINE} | awk -F'"' '{print $6}' )
	DATE_NOW=$( date "+%Y-%m-%d %H:%M:%S" )
	echo "[ ${DATE_NOW} ] - ${COUNT}/${N_LINE} - MESSAGE_TYPE=${MESSAGE_TYPE}"
	for BIND in ${BIND_EWKEY_ROUTE}; do
		EWKEY=$( echo ${BIND} | awk -F":" '{print $1}' )
		ROUTE=$( echo ${BIND} | awk -F":" '{print $2}' )

		#
		if [[ "${MESSAGE_TYPE}" == "${EWKEY}" ]]; then
			HTTP_CODE=$( ${COMMAND} "http://${WS_HOST}/api/eventdb/ew/${ROUTE}/1/" -H  "accept: application/json" -H  "content-type: application/json" -d "{ \"data\": ${LINE} }" )
                	if (( ${HTTP_CODE} != 201 )) && (( ${HTTP_CODE} != 200 )); then
				echo "LINE=${LINE}"
				echo ""
                        	echo "HTTP_CODE=${HTTP_CODE}"
				echo ""
                        	head -20 ${DIRNAME}/${FILE_OUTPUT}
				echo ""
                        	#exit
                	fi
		fi
	done
	COUNT=$(( ${COUNT} + 1 ))
done < ${EW_INPUT_FILE}

# Remove FILE_OUTPUT
if [ -f ${DIRNAME}/${FILE_OUTPUT} ]; then
	rm ${DIRNAME}/${FILE_OUTPUT}
fi
