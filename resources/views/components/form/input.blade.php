<?php
    $name = empty($options['name']) ? time().'input' : $options['name'];
    $id = empty($options['options']['id']) ? '' : $options['options']['id'];
    $cloneable = empty($options['cloneable']) ? false : $options['cloneable'];
    $type = empty($options['type']) ? 'text' : $options['type'];
    $value= empty($options['value']) ? '' : $options['value'];

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
    @if($options['label'] != null)
    <label id="label-{{$name}}" for="{{$id}}"><b>{!!$options['label']!!}</b></label>
    @endIf
    <?= Form::input($type, $name, $value, $options['options']) ?>
    <small id="small-{{$name}}" class="form-text text-danger">{{ isset($errors) ? $errors->first($name) : "" }}</small>
</div>