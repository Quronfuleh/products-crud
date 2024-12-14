<form id="edit-product-form">
    @csrf
    @method('PUT')

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

    <input type="hidden" name="id" id="edit_id" value="{{ $product['id'] }}">
    <div class="modal-header">
        <h5 class="modal-title">Edit Product: {{ $product['product_name'] }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" id="edit-errors-container">
        
        <div class="mb-3">
            <label for="edit_product_name" class="form-label">Product Name</label>
            <input type="text" id="edit_product_name" name="product_name" class="form-control" value="{{ $product['product_name'] }}" required>
        </div>
        <div class="mb-3">
            <label for="edit_quantity_in_stock" class="form-label">Quantity in stock</label>
            <input type="number" id="edit_quantity_in_stock" name="quantity_in_stock" class="form-control" value="{{ $product['quantity_in_stock'] }}" required min="0">
        </div>
        <div class="mb-3">
            <label for="edit_price_per_item" class="form-label">Price per item</label>
            <input type="number" step="0.01" id="edit_price_per_item" name="price_per_item" class="form-control" value="{{ $product['price_per_item'] }}" required min="0">
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>
