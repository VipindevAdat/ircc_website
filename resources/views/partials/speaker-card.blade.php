@php
    // First check if image exists in speakers directory (public_html/front/images/speakers/)
    $speakerName = $speaker->name;
    $speakerImageUrl = null;
    
    // Try multiple filename variations based on speaker name
    // Remove common prefixes and normalize
    $normalizedName = preg_replace('/^(dr|mr|mrs|ms|prof)\.?\s*/i', '', $speakerName);
    $normalizedName = trim($normalizedName);
    
    // Generate possible filename variations
    $possibleNames = [
        strtolower(str_replace([' ', '-', '.', ','], ['', '', '', ''], $normalizedName)),
        strtolower(str_replace([' ', '-'], ['_', '_'], $normalizedName)),
        strtolower(str_replace(' ', '', $normalizedName)),
        strtolower($normalizedName),
        // Also try with original name
        strtolower(str_replace([' ', '-', '.', ','], ['', '', '', ''], $speakerName)),
        strtolower(str_replace([' ', '-'], ['_', '_'], $speakerName)),
        strtolower(str_replace(' ', '', $speakerName)),
        strtolower($speakerName),
    ];
    
    // Remove duplicates
    $possibleNames = array_unique($possibleNames);
    
    $extensions = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];
    
    // Check speakers directory first
    $speakersDir = public_path('front/images/speakers/');
    if (is_dir($speakersDir)) {
        foreach ($possibleNames as $name) {
            foreach ($extensions as $ext) {
                $imagePath = 'front/images/speakers/' . $name . '.' . $ext;
                if (file_exists(public_path($imagePath))) {
                    $speakerImageUrl = asset($imagePath);
                    break 2;
                }
            }
        }
    }
    
    // If not found in speakers directory, try media library
    if (!$speakerImageUrl) {
        $media = $speaker->getMedia('team')->first();
        $speakerImageUrl = $media ? \App\Models\Team::publicUrlForMedia($media) : null;
    }
    
    // Final fallback
    $imageUrl = $speakerImageUrl ?: asset('front/images/user-img.png');
@endphp
<div class="col-lg-3">
    <div class="volunteer_wrapper">
        <div class="volunteer_img">
            <img src="{{ $imageUrl }}" alt="{{ $speaker->name }}" onerror="this.src='{{ asset('front/images/user-img.png') }}'">
        </div>
        <div class="volunteer_text">
            <h4>{{$speaker->name}}</h4>
            <span>{{$speaker->designation}}</span>
            <p>{{$speaker->organization}}</p>
        </div>
    </div>
</div>
