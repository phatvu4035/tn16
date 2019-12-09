<?php
    use Illuminate\Support\Facades\Input;

    $name = empty($options['name']) ? time().'input' : $options['name'];
    $id = empty($options['options']['id']) ? '' : $options['options']['id'];
    $cloneable = empty($options['cloneable']) ? false : $options['cloneable'];
    $type = empty($options['type']) ? 'text' : $options['type'];
    $value= empty($options['value']) ? '' : $options['value'];
    $label = empty($options['label']) ? '' : $options['label'];
    $placeholder = empty($options['options']['placeholder']) ? '' : $options['options']['placeholder'];
    $option = empty($options['options']) ? [] : $options['options'];
    $readonly = empty($options['options']['readonly']) ? '' : $options['options']['readonly'] == true ? 'readonly' : '';
    $required = empty($options['options']['required']) ? '' : $options['options']['required'] == true ? 'required' : '';
    // $value = Input::get($name);

    if($id != "" && $cloneable) {
        $id = $options['options']['id'] .= 1;
    }

    if(empty($id)) {
        $id = $cloneable ? $name . 1 : $name;
        if (empty($options['options'])) {
            $options['options'] = [
                'id' => $id
            ];
        } else {
            $options['options']['id'] = $id;
        }
    }
    
    if(!empty($options['options']['class'])) {
        $options['options']['class'] .= ' form-control ';
    } else {
        $options['options']['class'] = 'form-control';
    }

    $smallText = empty($options['smallMessage']) ? '' : $options['smallMessage'];
?>
<div>
    <label for="{{$id}}"><b>{!!$label!!}</b></label>
    <textarea {{$required}} id="{{$id}}" placeholder="{{$placeholder}}" class="{{$options['options']['class']}}" row="5" style="width:100%" name="{{$name}}" id="{{$id}}" {{$readonly}} data-value="{{$value}}">{{$value}}</textarea>
    <small id="small-{{$name}}" class="form-text text-danger">{{ isset($errors) ? $errors->first($name) : "" }}</small>
</div>