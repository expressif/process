<?php
/**
 * Expressif- process implementation
 * @author Ioan CHIRIAC
 * @license MIT
 */
namespace Expressif\Process {

  use Expressif\Stream\EventEmitter;

  /**
   * Handles a list of workers and detect when they ended
   */
  class Stack extends EventEmitter {
    public $workers = array();

    /**
     * Adds a new worker to the stack
     */
    public function add($target) {
      if (!($target instanceof Worker)) {
        $target = new Worker($target);
      }
      $this->workers[$target->pid] = $target;
      $target->forward($this);
      return $target;
    }

    /**
     * Check all process status
     */
    public function emit($event, array $args = []) {
      parent::emit($event, $args);
      if ($event === 'exit') {
        unset($this->workers[$args[0]->pid]);
        if (empty($this->workers)) {
          $this->emit('done');
        }
      }
      return $this;
    }
  }
}