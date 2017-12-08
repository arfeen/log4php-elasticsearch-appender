<?php

/**
 *
 * @package log4php
 */

/**
 * Appends events to ElasticSearch.
 *
 * This appender can be configured by changing the following attributes:
 * 
 * - remoteHost - Target remote host.
 * - port       - Target port (optional, defaults to 9200).
 * - esIndex    - Target Index for ElasticSearch.
 * - esType     - Target Document Type for ElasticSearch.
 *
 * 
 * @author Arfeen
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderElasticSearch extends LoggerAppender {

    /**
     * Target ElasticSearch host.
     * @see https://www.elastic.co/ 
     */
    protected $remoteHost;

    /** Target ElasticSearch port 
     */
    protected $port = 9200;

    /** Target ElasticSearch Index
     */
    protected $esIndex;

    /** Target ElasticSearch Document Type
     */
    protected $esType;

    // ******************************************
    // *** Appender methods                   ***
    // ******************************************

    /** Override the default layout to use serialized. 
     */
    public function getDefaultLayout() {
        return new LoggerLayoutSerialized();
    }

    public function activateOptions() {
        if (empty($this->remoteHost)) {
            $this->warn("Required parameter [remoteHost] not set. Closing appender.");

            return;
        }
    }

    public function append(LoggerLoggingEvent $event) {

        $server = $this->postData($this->layout->format($event));

        if ($server === false) {
            $this->warn("Could not connect to elasticsearch server {$this->remoteHost}:{$this->port}.");

            return;
        }
    }

    // ******************************************
    // *** Accessor methods                   ***
    // ******************************************

    /** Sets the target host. */
    public function setRemoteHost($hostname) {
        $this->setString('remoteHost', $hostname);
    }

    /** Sets the target port */
    public function setPort($port) {
        $this->setPositiveInteger('port', $port);
    }

    /** Sets the Index. */
    public function setesIndex($esindex) {
        $this->esIndex = $esindex;
    }

    /** Sets the Type. */
    public function setesType($estype) {
        $this->esType = $estype;
    }

    /** Returns the target host. */
    public function getRemoteHost() {
        return $this->getRemoteHost();
    }

    /** Returns the target port. */
    public function getPort() {
        return $this->port;
    }

    /** Returns the timeout */
    public function getTimeout() {
        return $this->timeout;
    }

    public function postData($params = "") {

        /* Unique ID for this json document */

        $esid = md5(uniqid() . microtime(true));

        $uri = $this->esIndex . '/' . $this->esType . '/' . $esid;

        $url = 'http://' . $this->remoteHost . ':' . $this->port . '/' . $uri;

        $curlrequest = curl_init();
        curl_setopt($curlrequest, CURLOPT_URL, $url);
        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlrequest, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlrequest, CURLOPT_HTTPHEADER, array(
            'method' => "POST",
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params)));

        $result = curl_exec($curlrequest);
        $httpcode = curl_getinfo($curlrequest, CURLINFO_HTTP_CODE);
        curl_close($curlrequest);

        if ($httpcode != 201) {
            $this->warn("Counld not create request to " . $this->remoteHost . ":" . $this->port . ".");
        }

        return $result;
    }

}
