<?php
/**
 * Created by PhpStorm.
 * User: AndrewKaretas
 * Date: 8/6/2018
 * Time: 3:49 PM
 */
namespace includes\objects;

class Plugin
{
    /**
   * @var int
   */
    public $id;

    /**
     * @var string
     */
    public $plugin_name;

    /**
     * @var string
     */
    public $current_version;

    /**
     * @var string
     */
    public $updated_version;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Plugin
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPluginName() {
        return $this->plugin_name;
    }

    /**
     * @param string $plugin_name
     * @return Plugin
     */
    public function setPluginName($plugin_name) {
        $this->plugin_name = $plugin_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentVersion() {
        return $this->current_version;
    }

    /**
     * @param string $current_version
     * @return Plugin
     */
    public function setCurrentVersion($current_version) {
        $this->current_version = $current_version;
        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedVersion() {
        return $this->updated_version;
    }

    /**
     * @param string $updated_version
     * @return Plugin
     */
    public function setUpdatedVersion($updated_version) {
        $this->updated_version = $updated_version;
        return $this;
    }

}

/**
 * id mediumint(9) NOT NULL AUTO_INCREMENT,
`plugin_name` text NOT NULL,
`current_version` text NOT NULL,
`updated_version` text NOT NULL,
 */