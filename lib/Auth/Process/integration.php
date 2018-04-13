<?php
class sspmod_comanageintegration_Auth_Process_integration extends SimpleSAML_Auth_ProcessingFilter
{
    private $config = null; 

    public function __construct($config, $reserved)
    {
        assert('is_array($config)');
        $this->config = $config;
        parent::__construct($config, $reserved);
    }

    public function process(&$state)
    {
        assert('is_array($state)');

        $attributes =& $state['Attributes'];
        $dbconfig = $this->config['db'];

        $nameId = 'urn:oid:1.3.6.1.4.1.5923.1.1.1.6';
        if(in_array('nameId', $this->config))
            $nameId = $this->config['nameId'];

        try {
            $db = new PDO($dbconfig['host'], $dbconfig['user'], $dbconfig['password']);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->exec("SET NAMES 'utf8'");

            $spid = $state['saml:sp:State']['core:SP'];
            $nameId = $attributes[$nameId][0];

            $ismemberof_string = "select name from cm_co_groups where status = 'A' and id = (select distinct co_group_id as GID from cm_co_group_members where co_person_id=(SELECT id FROM cm_co_people WHERE actor_identifier='".addslashes($nameId)."' AND co_person_id is NULL) and co_group_id = (select distinct co_group_id from cm_co_services where service_label = '".addslashes($spid)."' AND co_service_id is NULL));";

            $st = $db->prepare($ismemberof_string);
            if(!$st->execute(array('spid' => $spid, 'nameId' => $nameId))) {
                throw new Exception('Failed to query database for user.');
            }

            $st->setFetchMode(PDO::FETCH_ASSOC);

            if(in_array('urn:oid:1.3.6.1.4.1.5923.1.5.1.1', $attributes)) {
                if(is_array($attributes['urn:oid:1.3.6.1.4.1.5923.1.5.1.1']) == False) {
                    $attributes['urn:oid:1.3.6.1.4.1.5923.1.5.1.1'] = array(
                        $attributes['urn:oid:1.3.6.1.4.1.5923.1.5.1.1']
                    );
                }
            } else {
                $attributes['urn:oid:1.3.6.1.4.1.5923.1.5.1.1'] = array();
            }

            while($row = $st->fetch()) {
                array_push($attributes['urn:oid:1.3.6.1.4.1.5923.1.5.1.1'], $row['name']);
            }
        } catch (Exception $e) {
            SimpleSAML_Logger::error("Attribute query failed: ".$e->getMessage());
        }
    }
}
