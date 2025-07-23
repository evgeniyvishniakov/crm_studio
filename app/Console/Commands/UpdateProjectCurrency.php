<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\Project;

class UpdateProjectCurrency extends Command
{
    protected $signature = 'project:update-currency {project_id} {currency}';
    protected $description = 'Обновить валюту проекта';

    public function handle()
    {
        $projectId = $this->argument('project_id');
        $currency = $this->argument('currency');

        $project = Project::find($projectId);
        
        if (!$project) {
            $this->error("Проект с ID {$projectId} не найден!");
            return 1;
        }

        $project->currency = $currency;
        $project->save();

        $this->info("Валюта проекта '{$project->name}' обновлена на {$currency}");
        
        return 0;
    }
} 