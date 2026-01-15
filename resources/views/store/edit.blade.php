<x-app-layout title="Edit Store | Store Setting">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

    <style>
        #suggestion-list {
            position: absolute;
            z-index: 1000;
            background: white;
            width: 95%;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            list-style: none;
            padding: 0;
            margin-top: 2px;
            display: none;
        }
        #suggestion-list li {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }
        #suggestion-list li:hover {
            background-color: #f0f0f0;
            color: #4CAF50;
        }
    </style>

    <div class="container-fluid">

        <div class="py-3 d-flex align-items-center justify-content-between">
            <h4 class="fs-18 fw-semibold m-0">Edit Store: {{ $store->store_name }}</h4>
            <a href="{{ route('store.index') }}" class="btn btn-secondary btn-sm">
                <i class="mdi mdi-arrow-left"></i> Back to List
            </a>
        </div>

        <form action="{{ route('store.update', $store->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">General Information</h5>

                            <div class="row mb-4 align-items-center">
                                <div class="col-md-3 text-center">
                                    @if($store->profile)
                                        <img src="{{ asset('storage/' . $store->profile) }}" alt="Store Profile" 
                                             class="img-fluid rounded-circle border shadow-sm" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border mx-auto" 
                                             style="width: 80px; height: 80px;">
                                            <i class="mdi mdi-store fs-1 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label">Store Profile Image</label>
                                    <input type="file" name="profile" class="form-control" accept="image/*">
                                    <small class="text-muted">Allowed: jpg, png, webp. Max: 2MB</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Store Name <span class="text-danger">*</span></label>
                                    <input type="text" name="store_name" class="form-control"
                                        value="{{ old('store_name', $store->store_name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Store Code</label>
                                    <input type="text" class="form-control bg-light" value="{{ $store->store_code }}" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $store->email) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', $store->phone) }}">
                                </div>
                            </div>

                            <div class="mb-3 position-relative">
                                <label class="form-label">Address (Start typing to search)</label>
                                <textarea name="address" id="addr_street" class="form-control" rows="2" 
                                    placeholder="Type address here..." autocomplete="off">{{ old('address', $store->address) }}</textarea>
                                
                                <ul id="suggestion-list"></ul>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" id="addr_city" class="form-control"
                                        value="{{ old('city', $store->city) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" id="addr_state" class="form-control"
                                        value="{{ old('state', $store->state) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="country" id="addr_country" class="form-control"
                                        value="{{ old('country', $store->country) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" name="pincode" id="addr_pincode" class="form-control"
                                        value="{{ old('pincode', $store->pincode) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ $store->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$store->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Store Location</h5>
                            <p class="text-muted small">
                                Selecting an address will update the map. You can also drag the marker manually.
                            </p>

                            <div id="editMap" style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid #ddd;"></div>

                            <div class="row mt-3 d-none">
                                <div class="col-md-6">
                                    <label class="form-label">Latitude</label>
                                    <input type="text" name="latitude" id="latitude" class="form-control bg-light"
                                        value="{{ old('latitude', $store->latitude) }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Longitude</label>
                                    <input type="text" name="longitude" id="longitude" class="form-control bg-light"
                                        value="{{ old('longitude', $store->longitude) }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-content-save"></i> Update Store
                    </button>
                </div>
            </div>

        </form>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
        <script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. INITIALIZE MAP (Same as before) ---
        var lat = parseFloat("{{ $store->latitude ?? 28.6139 }}");
        var lng = parseFloat("{{ $store->longitude ?? 77.2090 }}");

        var map = L.map('editMap').setView([lat, lng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        // Update inputs on drag
        marker.on('dragend', function(e) {
            var pos = marker.getLatLng();
            updateCoordinates(pos.lat, pos.lng);
        });

        // Update inputs on map click
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
        });

        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(7);
            document.getElementById('longitude').value = lng.toFixed(7);
        }

        // --- 2. AUTOCOMPLETE LOGIC ---
        const addressInput = document.getElementById('addr_street');
        const suggestionList = document.getElementById('suggestion-list');
        let timeout = null;

        addressInput.addEventListener('input', function() {
            const query = this.value;
            clearTimeout(timeout);
            
            if (query.length < 3) {
                suggestionList.style.display = 'none';
                return;
            }

            timeout = setTimeout(() => {
                fetchSuggestions(query);
            }, 500);
        });

        function fetchSuggestions(query) {
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&addressdetails=1&limit=5`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    suggestionList.innerHTML = '';
                    if (data.length > 0) {
                        suggestionList.style.display = 'block';
                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.textContent = item.display_name;
                            li.addEventListener('click', () => selectAddress(item));
                            suggestionList.appendChild(li);
                        });
                    } else {
                        suggestionList.style.display = 'none';
                    }
                })
                .catch(err => console.error("Error fetching address:", err));
        }

        function selectAddress(item) {
            const newLat = parseFloat(item.lat);
            const newLon = parseFloat(item.lon);
            
            map.setView([newLat, newLon], 16);
            marker.setLatLng([newLat, newLon]);
            updateCoordinates(newLat, newLon);

            const addr = item.address;
            addressInput.value = (addr.road || addr.house_number) ? 
                                 `${addr.house_number ? addr.house_number + ', ' : ''}${addr.road || ''}` : 
                                 item.display_name.split(',')[0]; 
            
            document.getElementById('addr_city').value = addr.city || addr.town || addr.village || addr.city_district || addr.county || '';
            
            let state = addr.state || addr.state_district || addr.region || '';
            if (!state && (addr.city === 'Delhi' || addr.city === 'New Delhi')) {
                state = 'Delhi';
            }
            document.getElementById('addr_state').value = state;
            document.getElementById('addr_country').value = addr.country || '';
            document.getElementById('addr_pincode').value = addr.postcode || '';

            suggestionList.style.display = 'none';
        }

        document.addEventListener('click', function(e) {
            if (e.target !== addressInput && e.target !== suggestionList) {
                suggestionList.style.display = 'none';
            }
        });

        setTimeout(function() { map.invalidateSize(); }, 500);
    });
</script>
    @endpush

</x-app-layout>