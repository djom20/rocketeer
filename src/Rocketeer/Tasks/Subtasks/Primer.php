<?php

/*
 * This file is part of Rocketeer
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rocketeer\Tasks\Subtasks;

use Rocketeer\Tasks\AbstractTask;

/**
 * Executes some sanity-check commands before deploy.
 *
 * @author Maxime Fabre <ehtnam6@gmail.com>
 */
class Primer extends AbstractTask
{
    /**
     * A description of what the task does.
     *
     * @var string
     */
    protected $description = 'Run local checks to ensure deploy can proceed';

    /**
     * Whether to run the commands locally
     * or on the server.
     *
     * @var bool
     */
    protected $local = true;

    /**
     * Whether the task needs to be run on each stage or globally.
     *
     * @var bool
     */
    public $usesStages = false;

    /**
     * Run the task.
     *
     * @return bool
     */
    public function execute()
    {
        $tasks = $this->getHookedTasks('primer', [$this]);
        if (!$tasks) {
            return true;
        }

        $this->run($tasks);
        $status = $this->status();
        if (!$status) {
            $this->explainer->error('Project is not ready for deploy. You were almost fired.');
        }

        return $status;
    }
}
