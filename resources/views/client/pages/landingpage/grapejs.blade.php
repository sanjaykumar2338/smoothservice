@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }

    .custom-input-short{
        width: 11ch;
        font-size: 0.9375rem;
        font-weight: 400;
        line-height: 1.4;
        color: #677788;
        appearance: none;
        background-color: #fff;
        background-clip: padding-box;
        border: var(--bs-border-width) solid #d4d8dd;
        border-radius: var(--bs-border-radius);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        background-color: #fff;
    }

    /* Modal overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    /* Modal content */
    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        max-width: 890px;
        width: 100%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: relative;
        max-height: 800px;
        overflow-y: scroll;
    }

    /* Close button */
    .modal-close {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        font-size: 20px;
        background: none;
        border: none;
    }

    .option-input {
        margin-bottom: 10px;
        display: flex;
    }

    .option-menu {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
    }

    .add-option-menu {
        cursor: pointer;
        color: #007bff;
        text-decoration: underline;
    }

    .option-header {
        display: flex;
        align-items: center;
    }

    .option-header input {
        flex-grow: 1;
    }
</style>

<link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://malsup.github.io/jquery.blockUI.js"></script>
<script src="https://unpkg.com/grapesjs"></script>
<script src="https://unpkg.com/grapesjs-preset-webpage@1.0.2"></script>
<script src="https://unpkg.com/grapesjs-plugin-forms@2.0.5"></script>
<script src="https://unpkg.com/grapesjs-blocks-basic@1.0.1"></script>

<div class="container-xxl flex-grow-1 container-p-y" id="app">
    <div id="gjs" style=""></div>
</div>
<script>
    var editor = grapesjs.init({
      container : '#gjs',
      plugins: ['grapesjs-preset-webpage','grapesjs-plugin-forms','gjs-blocks-basic'],
      pluginsOpts: {
        'grapesjs-preset-webpage': {
        },
        'grapesjs-plugin-forms': {
        },
        "gjs-blocks-basic": {
        
        },     
    }
    });
</script>

@endsection
