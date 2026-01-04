<?php

namespace Drupal\acdweb_tools\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;

class TaskController extends ControllerBase {

  /**
   * Marks a task node as done and redirects back.
   */
  public function markDone(NodeInterface $node) {
    // 1. Validation
    if ($node->bundle() !== 'to_do') {
      return $this->redirect('entity.node.canonical', ['node' => $node->id()]);
    }

    // 2. Action: Set field_status to true (1)
    if ($node->hasField('field_status') && $node->get('field_status')->value != 1) {
      $node->set('field_status', TRUE);
      $node->save();
      $this->messenger()->addStatus($this->t('Task marked as done.'));
    }
    else {
      $this->messenger()->addStatus($this->t('Task is already done.'));
    }

    // 3. Redirect back to the task (clean URL)
    return $this->redirect('entity.node.canonical', ['node' => $node->id()]);
  }
}