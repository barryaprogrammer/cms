@extends('layouts.main')

@section('content')

<div class="col-md-3 ">
    @include('shared.sidebar')
</div>

<div class="col-md-9 column">

    <h2>Update Project: {{$project->title}} </h2>

    {{ Form::model($project, array('method' => 'PUT', 'route' => array('projects.update', $project->id), 'files' => 'true', 'role' => 'form')) }}


    <div class="form-group">
        <label>Project Name (<a href="http://www.restorationtrades.com/help.html#project_name" target="_blank">Help</a>)</label>
        {{ Form::text('title', null, array('class' => 'form-control')) }}
    </div>
    @if($errors->first('title'))
    <div class="alert alert-danger">
        {{  $errors->first('title'); }}
    </div>
    @endif

    <div class="form-group">
        <label>Project Browser Description (a.k.a. Title Tag) (<a href="http://www.restorationtrades.com/help.html#project_browser_description" target="_blank">Help</a>)</label>
        {{ Form::text('seo', null, array('class' => 'form-control')) }}
    </div>
    @if($errors->first('seo'))
    <div class="alert alert-danger">
        {{  $errors->first('seo'); }}
    </div>
    @endif

    <div class="form-group">
        <label>Project City and/or County (<a href="http://www.restorationtrades.com/help.html#project_city_county" target="_blank">Help</a>)</label>
        {{ Form::text('city_county', null, array('class' => 'form-control')) }}
    </div>
    @if($errors->first('city_county'))
    <div class="alert alert-danger">
        {{  $errors->first('city_county'); }}
    </div>
    @endif

    <div class="form-group">
        <label>Project State and Country (<a href="http://www.restorationtrades.com/help.html#project_state_country" target="_blank">Help</a>)</label>
        {{ Form::text('state_country', null, array('class' => 'form-control')) }}
    </div>
    @if($errors->first('state_country'))
    <div class="alert alert-danger">
        {{  $errors->first('state_country'); }}
    </div>
    @endif

    <div class="form-group">
        <label>Project Main Body (<a href="http://www.restorationtrades.com/help.html#project_main_body" target="_blank">Help</a>)</label>
        {{ Form::textarea('body', null, array('rows' => 30, 'class' => 'ckeditor form-control')) }}
    </div>
    @if($errors->first('body'))
    <div class="alert alert-danger">
        {{  $errors->first('body'); }}
    </div>
    @endif


    <div class="form-group">
        <div class="controls">
            <div class="checkbox">
                <label class="checkbox">{{ Form::checkbox('published', 1) }} Project Publish Status (<a href="http://www.restorationtrades.com/help.html#project_publish_status" target="_blank">Help</a>)</label>
            </div>
        </div>
    </div>

    @if($settings->theme != true)
    <div class="form-group">
        <label for="email">Related Portfolios</label>&nbsp;
        {{ Form::select('portfolio_id', $portfolios, $project->portfolio_id, array('class' => 'form-control', 'tabindex' => 1)) }}
        @if($errors->first('order'))
        <div class="alert alert-danger">
            {{  $errors->first('portfolio_id'); }}
        </div>
        @endif
    </div>
    @endif

    <!--sort order-->

    <div class="form-group">
        <label for="email">Sort Order</label>&nbsp;
        {{ Form::selectRange('order', 1, 10, array('class' => 'form-control', 'tabindex' => 1)) }}
        @if($errors->first('order'))
        <div class="alert alert-danger">
            {{  $errors->first('order'); }}
        </div>
        @endif
    </div>

    @include('shared.tags', array('model' => 'projects'))
    <br>

    <div class="form-group">
        <label>Project Web Address (URL) (<a href="http://www.restorationtrades.com/help.html#project_web_address" target="_blank">Help</a>)</label>
        {{ Form::text('slug', null, array('class' => 'form-control')) }}
        <div class="help-block">The url must start with / </div>
    </div>
    @if($errors->first('slug'))
    <div class="alert alert-danger">
        @if($errors->first('slug'))
        {{ $errors->first('slug') }}
        @endif
    </div>
    @endif
    <!-- image -->

    <div class="form-group">
        <label for="email">Project Default Image Uploader (<a href="http://www.restorationtrades.com/help.html#project_default_image_uploader" target="_blank">Help</a>)</label>
        {{ Form::file('image', null, array('class' => 'form-control', 'tabindex' => 1)) }}
        @if($errors->first('image'))
        <div class="alert alert-danger">
            {{  $errors->first('image'); }}
        </div>
        @endif
        @if($project->image)
        <div class="row">
            <div>
                <img  class="col-lg-4" src="/{{$path}}/{{$project->image}}" class="banner-show">
            </div>
        </div>
        @endif
        <div class="help-block">This is the image we will use for the default project image</div>
    </div>

    <br>

    <!-- images upload -->
    <label>Project Blowup Images Uploader (<a href="http://www.restorationtrades.com/help.html#project_blowup_image_uploader" target="_blank">Help</a>)</label>
    @include('shared.images_angular', array('model' => 'projects'))

    <br>
    <br>
    <!-- end images upload -->

    <div class="controls row">
        <div class="col-lg-2">
            {{ Form::submit('Update Project', array('id' => 'submit', 'class' => 'btn btn-success')) }}
            {{ Form::close() }}
        </div>
        <div class="col-lg-2">
            {{ Form::open(array('class' => 'inline', 'method' => 'DELETE', 'route' => array('projects.destroy', $project->id))) }}
            {{ Form::submit('Delete', array('class' => 'btn btn-danger', 'onclick' => 'return confirm("Are you sure you want to delete this?")')) }}
            {{ Form::close() }}
        </div>

        <br>
    </div>



</div>


@stop
