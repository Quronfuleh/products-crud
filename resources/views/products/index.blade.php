@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Products</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Something went wrong:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="ajax-errors"></div>

    <form id="product-form" class="mb-5">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" id="product_name" name="product_name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="quantity_in_stock" class="form-label">Quantity in stock</label>
                <input type="number" id="quantity_in_stock" name="quantity_in_stock" class="form-control" required min="0">
            </div>
            <div class="col-md-4">
                <label for="price_per_item" class="form-label">Price per item</label>
                <input type="number" step="0.01" id="price_per_item" name="price_per_item" class="form-control" required min="0">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>

    <table class="table table-bordered" id="products-table">
        <thead>
            <tr>
                <th>Product name</th>
                <th>Quantity in stock</th>
                <th>Price per item</th>
                <th>Datetime submitted</th>
                <th>Total value number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSum = 0; @endphp
            @foreach($products as $product)
                @php
                    $total = $product['quantity_in_stock'] * $product['price_per_item'];
                    $totalSum += $total;
                @endphp
                <tr data-id="{{ $product['id'] }}">
                    <td class="pname">{{ $product['product_name'] }}</td>
                    <td class="pqty">{{ $product['quantity_in_stock'] }}</td>
                    <td class="pprice">{{ $product['price_per_item'] }}</td>
                    <td>{{ $product['datetime_submitted'] }}</td>
                    <td class="ptotal">{{ $total }}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary edit-btn">Edit</button>
                        <form action="{{ route('products.destroy', $product['id']) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Delete this product?');">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"><strong>Total</strong></td>
                <td colspan="2" id="sum-total"><strong>{{ $totalSum }}</strong></td>
            </tr>
        </tbody>
    </table>
</div>


<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" id="edit-modal-content"></div>
  </div>
</div>
@endsection

@section('scripts')
<script>

    
$(function(){
    $('#product-form').on('submit', function(e) {
        e.preventDefault();
        $('#ajax-errors').empty();

        $.post("{{ route('products.store') }}", $(this).serialize())
            .done(function(res) {
                if(res.status === 'success') {
                    addRow(res.product);
                    $('#product-form')[0].reset();
                }
            })
            .fail(function(xhr) {
                handleAjaxErrors(xhr, '#ajax-errors');
            });
    });

    function addRow(product) {
        let total = product.quantity_in_stock * product.price_per_item;
        let row = `<tr data-id="${product.id}">
            <td class="pname">${product.product_name}</td>
            <td class="pqty">${product.quantity_in_stock}</td>
            <td class="pprice">${product.price_per_item}</td>
            <td>${product.datetime_submitted}</td>
            <td class="ptotal">${total}</td>
            <td>
                <button class="btn btn-sm btn-secondary edit-btn">Edit</button>
                <form action="{{ url('products') }}/${product.id}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Delete this product?');">Delete</button>
                </form>
            </td>
        </tr>`;

        $('#products-table tbody tr:last').before(row);
        updateTotal();
    }

    $(document).on('click', '.edit-btn', function(){
        let id = $(this).closest('tr').data('id');
        $('#ajax-errors').empty();
        $.get("{{ url('products') }}/" + id + "/edit", function(data){
            $('#edit-modal-content').html(data);
            $('#editModal').modal('show');
        });
    });

    $(document).on('submit', '#edit-product-form', function(e){
        e.preventDefault();
        $('#ajax-errors').empty();
        let id = $('#edit_id').val();

        $.ajax({
            url: "{{ url('products') }}/" + id,
            type: "PUT",
            data: $(this).serialize()
        }).done(function(res){
            if(res.status === 'success') {
                updateRow(id, res.product);
                $('#editModal').modal('hide');
            }
        }).fail(function(xhr){
            handleAjaxErrors(xhr, '#edit-modal-content');
        });
    });

    function updateRow(id, product) {
        let row = $('#products-table tr[data-id="'+id+'"]');
        row.find('.pname').text(product.product_name);
        row.find('.pqty').text(product.quantity_in_stock);
        row.find('.pprice').text(product.price_per_item);
        row.find('.ptotal').text(product.quantity_in_stock * product.price_per_item);
        updateTotal();
    }

    function updateTotal(){
        let sum = 0;
        $('#products-table .ptotal').each(function(){
            sum += parseFloat($(this).text()) || 0;
        });
        $('#sum-total').text(sum);
    }

    function handleAjaxErrors(xhr, container) {
        if (xhr.status === 422) {
            let errors = xhr.responseJSON.errors;
            let html = '<div class="alert alert-danger"><ul>';
            $.each(errors, function(_, msgs) {
                $.each(msgs, function(_, msg) {
                    html += '<li>' + msg + '</li>';
                });
            });
            html += '</ul></div>';
            $(container).html(html);
        } else {
            $(container).html('<div class="alert alert-danger">Error!</div>');
        }
    }
});
</script>
@endsection
