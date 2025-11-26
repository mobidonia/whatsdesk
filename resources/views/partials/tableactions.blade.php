<?php
$param=[];
$param[$parameter_name]=$item->id;
?>
<td>
    <a href="{{ route( $webroute_path."edit",$param) }}" class="btn btn-primary btn-sm">{{ __('crud.edit') }}</a>
    <a href="{{ route( $webroute_path."delete",$param) }}" class="btn btn-danger btn-sm">{{ __('crud.delete') }}</a>
</td>