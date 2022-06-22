<a href="javascript:void(0)"
   onclick="updateEntry(this)"
   data-route="{{ url($crud->route.'/'.$entry->getKey().'/update_status_url') }}"
   class="btn btn-sm btn-link"
   data-button-type="patch"
><i class="las la-{{$entry->status == \App\Enum\UrlStatus::RUNNING ? 'pause' : 'running'}}"></i> {{$entry->status == \App\Enum\UrlStatus::RUNNING ? 'Stop Crawl' : 'Start Run'}}
</a>

<script>
    if (typeof updateEntry != "function") {
        $("[data-button-type=patch]").unbind("click");

        function updateEntry(button) {
            var route = $(button).attr("data-route");

            swal({
                title: "Do you change status for this URL Crawl ?",
                icon: "info",
                buttons: ["Cancel", "Yes"],
                dangerMode: true,
            }).then((value) => {
                if (value) {
                    $.ajax({
                        url: route,
                        type: "post",
                        success: function (result) {
                            if (result == 1) {
                                if (
                                    typeof crud != "undefined" &&
                                    typeof crud.table != "undefined"
                                ) {
                                    // Move to previous page in case of deleting the only item in table
                                    if (crud.table.rows().count() === 1) {
                                        crud.table.page("previous");
                                    }

                                    crud.table.draw(false);
                                }

                                // Show a success notification bubble
                                new Noty({
                                    type: "success",
                                    text: "Updated URL Status",
                                }).show();

                                // Hide the modal, if any
                                $(".modal").modal("hide");
                            } else {
                                // if the result is an array, it means
                                // we have notification bubbles to show
                                if (result instanceof Object) {
                                    // trigger one or more bubble notifications
                                    Object.entries(result).forEach(function (
                                        entry,
                                        index
                                    ) {
                                        var type = entry[0];
                                        entry[1].forEach(function (message, i) {
                                            new Noty({
                                                type: "Success",
                                                text: "Updated URL Status",
                                            }).show();
                                        });
                                    });
                                } else {
                                    // Show an error alert
                                    swal({
                                        title: "Failure",
                                        icon: "error",
                                        timer: 4000,
                                        buttons: false,
                                    });
                                }
                            }
                        },
                        error: function (result) {
                            // Show an alert with the result
                            swal({
                                title: "Failure",
                                icon: "error",
                                timer: 4000,
                                buttons: false,
                            });
                        },
                    });
                }
            });
        }
    }

    // make it so that the function above is run after each DataTable draw event
    // crud.addFunctionToDataTablesDrawEventQueue('updateEntry');
</script>
@if (!request()->ajax())
    @endpush
@endif
