<?php

namespace App\Livewire;

use App\Models\CustomField;
use App\Models\CustomFieldValue;
use Livewire\Component;

class CustomFieldManager extends Component
{
    public $projectId;
    public $showCreateModal = false;

    public $name = '';
    public $type = 'text';
    public $options = '';
    public $isRequired = false;

    public function mount($projectId)
    {
        $this->projectId = $projectId;
    }

    public function createField()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,select,checkbox',
        ]);

        $opts = null;
        if ($this->type === 'select' && $this->options) {
            $opts = array_map('trim', explode(',', $this->options));
        }

        CustomField::create([
            'project_id' => $this->projectId,
            'name' => $this->name,
            'type' => $this->type,
            'options' => $opts,
            'is_required' => $this->isRequired,
            'sort_order' => CustomField::where('project_id', $this->projectId)->max('sort_order') + 1,
        ]);

        $this->reset(['name', 'type', 'options', 'isRequired', 'showCreateModal']);
    }

    public function deleteField($fieldId)
    {
        CustomField::where('id', $fieldId)->where('project_id', $this->projectId)->delete();
    }

    public function getFieldsProperty()
    {
        return CustomField::where('project_id', $this->projectId)->orderBy('sort_order')->get();
    }

    public function render()
    {
        return view('livewire.custom-field-manager');
    }
}
