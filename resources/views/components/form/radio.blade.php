<?php
    $name = empty($options['name']) ? time().'input' : $options['name'];
    $data = empty($options['data']) ? [] : $options['data'];
    $inputClass = empty($options['inputClass']) ? '' : $options['inputClass'];
    $label = empty($options['label']) ? '' : $options['label'];
    $class = empty($options['class']) ? 'form-check col-md-12' : $options['class']. ' form-check';
    $disabled = empty($options['options']['disabled']) ? false : $options['options']['disabled'];

    $isFirst = true;
?>
<div>
    <label><b>{!!$label!!}</b></label>
</div>
<?php 
    foreach ($data as $key => $value) {
        ?>
            <div class="{{$class}}">
                <label class="form-check-label">
                    <input 
                        class="form-check-input {{$inputClass}}" 
                        type="radio" 
                        name="{{$name}}" 
                        id="gridRadios1" 
                        value="{{$value}}"
                        {{$disabled ? 'disabled' : ''}}
                        <?= $isFirst ? 'checked="checked"' : '' ?>
                    > 
                    {{$key}}
                </label>
            </div>
        <?php
        $isFirst = false;
    }

?>