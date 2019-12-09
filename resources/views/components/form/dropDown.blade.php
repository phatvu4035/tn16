<?php
    $name = empty($options['name']) ? time().'input' : $options['name'];
    $id = empty($options['options']['id']) ? '' : $options['options']['id'];
    $cloneable = empty($options['cloneable']) ? false : $options['cloneable'];
    $disabled = empty($options['options']['disabled']) ? false : $options['options']['disabled'];
    $selected = empty($options['selected']) ? '0': $options['selected'];
    $disabledSelect = empty($options['disabled-select']) ? [] : $options['disabled-select'];
    $noDefault = empty($options['noDefault']) ? false: true;
    $label = empty($options['label']) ? "" : $options['label'];
    if(!empty($options['options']['id']) && $cloneable) {
        $id = $options['options']['id'] . 1;
    }

    if(empty($id)) {
        $id = empty($cloneable) || !$cloneable ? $name : $name . 1;
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
?>
<div>
    @if($label !== "")
    <label id="label-{{$id}}" for="{{$id}}"><b>{!!$options['label']!!}</b></label>
    @endIf()
    <select id="{{$id}}" class="{{$options['options']['class']}}" {{$disabled ? 'disabled' : ''}} name="{{$name}}">
        <?php if (!$noDefault) : ?>
            <option value="" <?= $selected == 0 ? 'selected' : ''?>>Chọn một mục . . .</option>
        <?php endIf; ?>
        <?php
            foreach ($options['data'] as $item => $value) {
                $disabledAttr = in_array($value, $disabledSelect) ? 'disabled' : '';
                ?>
                    <option <?= $disabledAttr ?> <?= $selected == $value ? 'selected' : ''?> value="{{$value}}" sele="{{$selected}}">{{$item}}</option>
                <?php
            }
        ?>
    </select>
    <small id="small-{{$name}}" class="form-text text-danger">{{ isset($errors) ? $errors->first($name) : "" }}</small>
</div>