<?php
class GitHubGoogleDriveGallery {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function fetch_folder_images($folder_id, $limit = 20) {
        $api_url = sprintf(
            'https://www.googleapis.com/drive/v3/files?q=\'%s\' in parents and mimeType contains \'image/\'&key=%s&pageSize=%d',
            $folder_id,
            $this->api_key,
            $limit
        );

        $response = file_get_contents($api_url);
        
        if (!$response) {
            return [];
        }

        $data = json_decode($response, true);
        $images = [];

        if (!empty($data['files'])) {
            foreach ($data['files'] as $file) {
                $images[] = [
                    'url' => sprintf('https://drive.google.com/uc?id=%s', $file['id']),
                    'name' => $file['name'] ?? 'Untitled Image'
                ];
            }
        }

        return $images;
    }

    public function generate_gallery_html($images) {
        if (empty($images)) {
            return '<p>No images found.</p>';
        }

        $html = '<div class="gdrive-gallery">';
        foreach ($images as $image) {
            $html .= sprintf(
                '<div class="gdrive-image">
                    <img src="%s" alt="%s">
                    <p>%s</p>
                </div>',
                htmlspecialchars($image['url']),
                htmlspecialchars($image['name']),
                htmlspecialchars($image['name'])
            );
        }
        $html .= '</div>';

        return $html;
    }

    public function create_gallery_page($folder_id) {
        $images = $this->fetch_folder_images($folder_id);
        return $this->generate_gallery_html($images);
    }
}

// Usage example
$api_key = 'YOUR_GOOGLE_DRIVE_API_KEY';
$folder_id = 'YOUR_GOOGLE_DRIVE_FOLDER_ID';

$gallery = new GitHubGoogleDriveGallery($api_key);
echo $gallery->create_gallery_page($folder_id);
?>
