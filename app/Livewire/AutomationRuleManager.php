<?php

namespace App\Livewire;

use App\Models\AutomationRule;
use App\Models\Project;
use Livewire\Component;

class AutomationRuleManager extends Component
{
    public $projectId;
    public $showCreateModal = false;

    public $name = '';
    public $triggerType = 'status_changed';
    public $triggerConditionFrom = '';
    public $triggerConditionTo = '';
    public $actionType = 'change_status';
    public $actionValue = '';
    public $isActive = true;

    public function mount($projectId)
    {
        $this->projectId = $projectId;
    }

    public function createRule()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'triggerType' => 'required|in:status_changed,due_date_reached,task_created,task_assigned',
            'actionType' => 'required|in:change_status,assign_user,send_notification,set_priority',
        ]);

        $conditions = [];
        if ($this->triggerType === 'status_changed') {
            $conditions = [
                'from' => $this->triggerConditionFrom ?: null,
                'to' => $this->triggerConditionTo ?: null,
            ];
        }

        AutomationRule::create([
            'project_id' => $this->projectId,
            'name' => $this->name,
            'trigger_type' => $this->triggerType,
            'trigger_conditions' => $conditions,
            'action_type' => $this->actionType,
            'action_data' => ['value' => $this->actionValue],
            'is_active' => $this->isActive,
            'created_by' => auth()->id(),
        ]);

        $this->reset(['name', 'triggerType', 'triggerConditionFrom', 'triggerConditionTo', 'actionType', 'actionValue', 'showCreateModal']);
    }

    public function toggleActive($ruleId)
    {
        $rule = AutomationRule::where('id', $ruleId)->where('project_id', $this->projectId)->first();
        if ($rule) {
            $rule->update(['is_active' => !$rule->is_active]);
        }
    }

    public function deleteRule($ruleId)
    {
        AutomationRule::where('id', $ruleId)->where('project_id', $this->projectId)->delete();
    }

    public function getRulesProperty()
    {
        return AutomationRule::where('project_id', $this->projectId)
            ->with('creator')
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.automation-rule-manager');
    }
}
