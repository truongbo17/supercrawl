<form action="{{ url($crud->route.'/import_url') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="file-input" class="btn btn-success">
            <span class="ladda-label"><i
                    class="la la-arrow-circle-up"></i> Import</span>
    </label>
    <input hidden class="file-input" id="file-input" accept="application/JSON" name="json_url" type="file" onchange="form.submit()"/>
</form>
