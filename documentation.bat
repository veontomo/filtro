REM first delete the previous log files
DEL "phpdoc-*.errors.log"
DEL "phpdoc-*.log"

phpdoc -d core -t docs