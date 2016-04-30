<?php

/**
 * @file
 * Contains \Drupal\elasticsearch_connector\Entity\Cluster.
 */

namespace Drupal\elasticsearch_connector\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Elasticsearch Connector Cluster configuration entity.
 *
 * @ConfigEntityType(
 *   id = "elasticsearch_cluster",
 *   label = @Translation("Elasticsearch Cluster"),
 *   handlers = {
 *     "list_builder" = "Drupal\elasticsearch_connector\Controller\ClusterListBuilder",
 *     "form" = {
 *       "default" = "Drupal\elasticsearch_connector\Form\ClusterForm",
 *       "delete" = "Drupal\elasticsearch_connector\Form\ClusterDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\elasticsearch_connector\Entity\ClusterRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer elasticsearch cluster",
 *   config_prefix = "cluster",
 *   entity_keys = {
 *     "id" = "cluster_id",
 *     "label" = "name",
 *     "status" = "status",
 *     "url" = "url",
 *     "options" = "options",
 *   },
 *   config_export = {
 *     "cluster_id",
 *     "name",
 *     "status",
 *     "url",
 *     "options",
 *   }
 * )
 */
class Cluster extends ConfigEntityBase {

  // Active status
  const ELASTICSEARCH_CONNECTOR_STATUS_ACTIVE = 1;

  // Inactive status
  const ELASTICSEARCH_CONNECTOR_STATUS_INACTIVE = 0;

  // Default connection timeout in seconds.
  const ELASTICSEARCH_CONNECTOR_DEFAULT_TIMEOUT = 3;
  /**
   * The cluster machine name.
   *
   * @var string
   */
  public $cluster_id;

  /**
   * The human-readable name of the cluster entity.
   *
   * @var string
   */
  public $name;

  /**
   * Status.
   *
   * @var string
   */
  public $status;

  /**
   * The cluster url.
   *
   * @var string
   */
  public $url;

  /**
   * Options of the cluster.
   *
   * @var array
   */
  public $options;

  /**
   * The locked status of this cluster.
   *
   * @var bool
   */
  protected $locked = FALSE;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return isset($this->cluster_id) ? $this->cluster_id : NULL;
  }

  /**
   * Get the default (cluster) used for elasticsearch.
   *
   * @return string
   *
   * TODO: Not sure that getting the default cluster in general should be part of the CLuster entity.
   * TODO: Maybe check if that is a default Cluster entity - YES, but in general - NO.
   */
  public static function getDefaultCluster() {
    return \Drupal::state()->get(
      'elasticsearch_connector_get_default_connector',
      ''
    );
  }

  /**
   * Set the default (cluster) used for elasticsearch.
   *
   * @param $cluster_id
   *
   * @return mixed
   */
  public static function setDefaultCluster($cluster_id) {
    return \Drupal::state()->set(
      'elasticsearch_connector_get_default_connector',
      $cluster_id
    );
  }

  /**
   * Load all clusters.
   *
   * @param bool $include_inactive
   *
   * @return \Drupal\elasticsearch_connector\Entity\Cluster[]
   */
  public static function loadAllClusters($include_inactive = TRUE) {
    $clusters = self::loadMultiple();
    foreach ($clusters as $cluster) {
      if (!$include_inactive && !$cluster->status) {
        unset($clusters[$cluster->cluster_id]);
      }
    }

    return $clusters;
  }

  /**
   * Get the full base URL of the cluster, including any authentication
   *
   * @param bool $safe If True (default), the the password will be starred out
   *
   * @return string
   */
  public function getBaseUrl($safe = TRUE) {
    $options = $this->options;
    if ($options['use_authentication']) {
      if ($options['username'] && $options['password']) {
        $schema = file_uri_scheme($this->url);
        $host = file_uri_target($this->url);
        $user = $options['username'];

        if ($safe) {
          return $schema . '://' . $user . ':****@' . $host;
        }
        else {
          return $schema . '://' . $user . ':' . $options['password'] . '@' . $host;
        }
      }
    }

    return $this->url;
  }
}
