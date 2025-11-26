@extends('general.index', $setup)
@section('tbody')
    @foreach ($setup['items'] as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->email }}</td>
            <?php
                $param=[];
                $param[$setup['parameter_name']]=$item->id;
            ?>
            <td>
                <a href="{{ route( $setup['webroute_path']."edit",$param) }}" class="btn btn-primary btn-sm">{{ __('crud.edit') }}</a>
                <a href="{{ route( $setup['webroute_path']."delete",$param) }}" class="btn btn-danger btn-sm">{{ __('crud.delete') }}</a>
                <a href="{{ route( $setup['webroute_path']."loginas",["agent"=>$item->id]) }}" class="btn btn-success btn-sm">{{ __('Login as') }}</a>
            </td>
        </tr> 
    @endforeach
@endsection