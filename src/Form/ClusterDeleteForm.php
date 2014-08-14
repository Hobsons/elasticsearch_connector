<?php

/**
 * @file
 * Contains \Drupal\elasticsearch\Form\ClusterDeleteForm.
 */

namespace Drupal\elasticsearch\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\elasticsearch\Entity\Cluster;

/**
 * Defines a confirmation form for deletion of a custom menu.
 */
class ClusterDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete the cluster %title?', array('%title' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, FormStateInterface $form_state) {
    if ($this->entity->id() == Cluster::getDefaultCluster()) {
      drupal_set_message($this->t('The cluster %title cannot be deleted as it is set as the default cluster.', array('%title' => $this->entity->label())), 'error');
    }
    else {
      $this->entity->delete();
      drupal_set_message($this->t('The cluster %title has been deleted.', array('%title' => $this->entity->label())));
    }
    $form_state->setRedirect('elasticsearch.clusters');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('elasticsearch.cluster_info', array('elasticsearch_cluster' => $this->entity->id()));
  }
}
