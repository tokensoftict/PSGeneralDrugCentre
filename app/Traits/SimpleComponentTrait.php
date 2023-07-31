<?php
namespace App\Traits;

use App\Classes\Settings;
use Illuminate\Database\Eloquent\Model;
use Jantinnerezo\LivewireAlert\LivewireAlert;

trait SimpleComponentTrait
{
    use LivewireAlert;

    public $model;

    protected $cacheModel = null;

    public array $data;

    public String $modalTitle = "New";

    public String $saveButton = "Save";

    public String $modalName = "";

    public $modelId;

    public array $newValidateRules;

    public array $editcallback;

    public array $updateValidateRules;


    public function boot()
    {
        $this->listeners = [
            'editData' => 'edit',
            'toggleData' => 'toggle',
            'destoryData' => 'destroy',
        ];

    }



    public function initControls()
    {
        foreach($this->data as $key=>$value)
        {
            $this->{$key} = "";
        }

    }

    public function save()
    {
        $this->validate($this->newValidateRules);

        $model = new $this->model();
        $model = $this->parseData($model);
        $model->save();
        $this->emit('refreshData',[]);
        $this->dispatchBrowserEvent("closeModal", []);
    }


    protected function parseData($model)
    {
        foreach($this->data as $key=>$value)
        {
            if($key === "password") {
                if(!empty($this->{$key})){
                    $model->{$key} = bcrypt($this->{$key});
                    continue;
                }else {
                    continue;
                }
            }
            $model->{$key} = $this->{$key} === "" ? NULL : $this->{$key};
        }
        return $model;
    }

    public function update($id)
    {
        $this->modelId = $id;

        $this->validate($this->updateValidateRules);

        $model = $this->model::find($id);
        $model = $this->parseData($model);
        $model->save();

        $this->emit('refreshData',[]);
        $this->emit(":refresh");
        $this->dispatchBrowserEvent("closeModal", []);
    }



    public function get()
    {
       return $this->cacheModel === null ? $this->model::paginate(Settings::$pagination) : call_user_func($this->cacheModel);
    }

    protected function loadEdit($id)
    {
        return $this->get()->filter(function($item) use($id){
            return $item->id === $id;
        })->first();
    }

    public function edit($id)
    {
        $this->modelId = $id;

        $data = $this->loadEdit($id);

        //$data = $this->model::find($id);

        foreach($this->data as $key=>$value)
        {
           if(empty($data->{$key})){
               $this->{$key} = "";
           }else {
               $this->{$key} = $data->{$key};
           }
        }

        if(isset($this->password)){
            $this->password = "";
        }

        $this->modalTitle = "Update";

        $this->saveButton = "Update";

        if(isset($this->editcallback) && count($this->editcallback) > 0)
        {
            foreach ($this->editcallback as $callback)
            {
                $this->$callback();
            }

        }


        $this->emit('refreshData',[]);
        $this->dispatchBrowserEvent("openModal", []);
    }


    public function new()
    {
        foreach($this->data as $key=>$value)
        {
            $this->{$key} = "";
        }

        $this->modalTitle = "New";

        $this->saveButton = "Save";

        $this->dispatchBrowserEvent("openModal", []);
    }

    public function toggle($id)
    {
        $this->modelId = $id;
        $model = $this->model::find($id);
        $model->status = !$model->status;
        $model->save();
        $this->emit('refreshData',[]);
    }

    public function destroy($id)
    {
        $this->modelId = $id;
        $this->model::find($id)->delete();
        $this->emit('refreshData',[]);
    }



}
