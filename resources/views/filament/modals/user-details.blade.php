<div>
    <style>
        label {
            color: #838383;
        }

    </style>
    <section class="grid grid-cols-2 gap-2 border rounded-lg border-[#838383] p-4">
        <div class="col-span-2">
            @if ($user->avatar_url)
            <div x-on:click="SimpleLightBox.open(event, '')">
                <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="{{ $user->name }} Avatar" class="w-36 h-36 object-cover rounded-full">
            </div>
            @else
            <img src="{{ asset('images/user_default.png') }}" alt="{{ $user->name }} Avatar" class="w-36 h-36 object-cover rounded-full simple-light-box-img-indicator">
            @endif
        </div>
        <div>
            <label class="font-medium">Name:</label>
            <p>{{ $user->name }}</p>
        </div>
        <div>
            <label class="font-medium">Gender:</label>
            <p class="capitalize">{{ $user->gender }}</p>
        </div>
        <div>
            <label class="font-medium">Date of Birth:</label>
            <p>
                @if ($user->date_of_birth)
                @php
                // Convert the date_of_birth string to a Carbon instance
                $dob = \Carbon\Carbon::parse($user->date_of_birth);
                // Format the date as "May 16th, 1999"
                $formattedDob = $dob->format('jS F, Y');
                // Calculate the age
                $age = $dob->age;
                @endphp
                {{ $formattedDob }} ({{ $age }} years)
                @else
                N/A
                @endif
            </p>
        </div>
        <div class="col-span-2">
            <label class="font-medium">Email:</label>
            <p>{{ $user->email }}</p>
        </div>
        <div>
            <label class="font-medium">Primary Contact No:</label>
            <p>{{ $user->primary_contact_number ?? "N/a" }}</p>
        </div>
        <div>
            <label class="font-medium">Secondary Contact No:</label>
            <p>{{ $user->secondary_contact_number ?? "N/a" }}</p>
        </div>
        <div>
            <label class="font-medium">Permanent Address:</label>
            <p>{{ $user->permanent_address }}</p>
        </div>
        <div>
            <label class="font-medium">Current Address:</label>
            <p>{{ $user->current_address }}</p>
        </div>
        <div>
            <label class="font-medium">In-Game ID:</label>
            <p>{{ $user->userGameInfos->where('game_id', $model->tournament->game_id)->first()->ingame_id }}</p>
        </div>
        <div>
            <label class="font-medium">In-Game Name:</label>
            <p>{{ $user->userGameInfos->where('game_id', $model->tournament->game_id)->first()->ingame_name ?? "N/a" }}</p>
        </div>
        <!-- Add more user details as needed -->
    </section>
    {{-- <section class="grid grid-cols-2 gap-2 mt-4 border rounded-lg border-[#838383] p-4">
        <div class="col-span-2 space-y-2">
            <label class="font-medium">Verification Document</label>
            @if ($user->verification_document_image_path)
            <div x-on:click="SimpleLightBox.open(event, '')" class="relative border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <img src="{{ asset('storage/' . $user->verification_document_image_path) }}" alt="image not loaded" class="w-full h-48 sm:h-60 object-cover hover:scale-105 transition-transform duration-300 simple-light-box-img-indicator" loading="lazy" crossorigin="anonymous">
            </div>
            @else
            <img src="{{ asset('images/document_default.png') }}" alt="image not loaded" class="w-full rounded-[2.67rem] lg:rounded-[4rem] object-cover hover:scale-105 transition-transform duration-300" loading="lazy" crossorigin="anonymous">
            @endif
        </div>
        <div class="col-span-2">
            <label class="font-medium">Document Number</label>
            <p>{{ $user->verification_document_number ?? "N/a" }}</p>
        </div>
        <div>
            <label class="font-medium">Issue Date</label>
            <p>{{ $user->verification_document_issue_date ?? "N/a" }}</p>
        </div>
        <div>
            <label class="font-medium">Expiry Date</label>
            <p>{{ $user->verification_document_expiry_date ?? "N/a" }}</p>
        </div>
    </section> --}}
</div>
