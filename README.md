# Log4PHP ElasticSearch Appender

This class can append log messages to ElasticSearch using Log4PHP. 


This class can append log messages to ElasticSearch using Log4PHP.

It extends the Log4PHP LoggerAppender class to send HTTP requests to an ElasticSearch instance REST API to log event messages.

This class must be copied in [log4phproot]/appenders .


Below is the usage details. This appender will be configured in "Log4PHP" appender configurations. 

Example configuration:

```
<?php

/* a sample configuration code for log4php. XML config can be used in same way. */

$configarray = array(
    'appenders' => array(
        'ElasticSearch' => array(
            'class' => 'LoggerAppenderElasticSearch',
            'layout' => array(
                'class' => 'LoggerLayoutPattern',
                'params' => array(
                    'conversionPattern' => json_encode(array(
                        'level' => "%p",
                        'ip' => "%X{ip}",
                        'action' => "%X{actor_id}",
                        'view' => "%X{view}",
                        'class_context' => "%X{class_context}",
                        "logtimestamp" => "%d{Y-m-d H:i:s}"
                    ))
                )
            ),
            'params' => array(
                'remoteHost' => 'localhost',
                'port' => '9200',
                'esIndex' => 'myindex',
                'esType' => 'mytype'
            ),
            'filters' => array(
                array(
                    'class' => 'LoggerFilterLevelRange',
                    'params' => array(
                        'levelMin' => 'info',
                        'levelMax' => 'fatal'
                    )
                )
            )
        )
    ),
    'rootLogger' => array(
        'level' => 'ALL',
        'appenders' => array(
            'ElasticSearch'
        )
    )
);



return $configarray;
?>
```
