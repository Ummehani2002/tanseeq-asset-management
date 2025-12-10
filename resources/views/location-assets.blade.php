@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4 text-center">Location Asset Lookup</h3>

    {{-- SEARCH BOX --}}
    <div class="mb-3 position-relative">
        <label for="location_name">Location</label>
        <input type="text" id="location_name" name="location_name" class="form-control" autocomplete="off">
        <div id="locationList"
             class="list-group"
             style="position:absolute; z-index:999; width:100%; max-height:200px; overflow-y:auto;">
        </div>
    </div>
    {{-- TABLE --}}
    <table class="table table-bordered mt-4">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Asset ID</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Serial Number</th>
                <th>PO Number</th>
                <th>Purchase Date</th>
                <th>Expiry Date</th>
            </tr>
        </thead>
        <tbody id="assetTableBody">
            <tr>
                <td colspan="8" class="text-center text-muted">
                    Type a location to view its assets.
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

   
    $('#location_name').on('keyup', function(){
        let query = $(this).val();

        if(query.length >= 1){
            $.ajax({
                url: "/locations-autocomplete"

                type: "GET",
                data: { query: query },
                success: function(data){
                    $('#locationList').fadeIn().empty();

                    if (data.length === 0) {
                        $('#locationList').append(
                            '<a class="list-group-item list-group-item-action disabled">No results found</a>'
                        );
                        return;
                    }

                    data.forEach(function(loc){
                        $('#locationList').append(`
                            <a href="#" class="list-group-item list-group-item-action location-item"
                               data-id="${loc.id}"

                               data-name="${loc.location_name}">
                                ${loc.location_name} (${loc.location_id})
                            </a>
                        `);
                    });
                }
            });
        } else {
            $('#locationList').fadeOut();
        }
    });

    //  Selecting from Dropdown
    $(document).on('click', '.location-item', function(e){
        e.preventDefault();
        let locationId = $(this).data('id');
        let locationName = $(this).data('name');

        $('#location_name').val(locationName);
        $('#locationList').fadeOut();

      
        $.ajax({
            url: `/locations/${locationId}/assets`,
            type: "GET",
            success: function(data){
                let tableBody = $('#assetTableBody');
                tableBody.empty();

                if (data.length > 0) {
                    data.forEach((asset, index) => {
                        tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${asset.asset_id}</td>
                                <td>${asset.category}</td>
                                <td>${asset.brand}</td>
                                <td>${asset.serial_number}</td>
                                <td>${asset.po_number}</td>
                                <td>${asset.purchase_date}</td>
                                <td>${asset.expiry_date}</td>
                            </tr>
                        `);
                    });
                } else {
                    tableBody.append(`
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No assets found for this location.
                            </td>
                        </tr>
                    `);
                }
            }
        });
    });
    $(document).click(function(e){
        if(!$(e.target).closest('#location_name, #locationList').length){
            $('#locationList').fadeOut();
        }
    });

});
</script>
@endsection
