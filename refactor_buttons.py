import os
import re
import glob

base_dir = 'd:/bengkel/bengkel/resources/views/admin'
files = glob.glob(os.path.join(base_dir, '**/index.blade.php'), recursive=True)

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Extract the 'Tambah' button from the header
    match = re.search(r'(<a\s+href=[^>]+class="[^"]*bg-\[#FF6B00\][^"]*".*?</a>)', content, re.DOTALL)
    
    if match:
        button_html = match.group(1)
        
        # Remove the button from the header
        content = content.replace(button_html, '')
        
        # Clean up the whitespace left behind
        content = re.sub(r'</h2>\s*</div>', '</h2>\n        </div>', content)
        
        # Now find the search bar div
        search_div_pattern = r'(<!-- Search Bar -->\s*<div class="mb-6 flex justify-between items-center">\s*<form.*?</form>\s*)'
        
        # Format the button spacing to match the new location
        formatted_button = button_html.replace('\n', '\n                        ')
        replacement = r'\1' + formatted_button + '\n                    '
        
        new_content = re.sub(search_div_pattern, replacement, content, flags=re.DOTALL)
        
        if content != new_content:
            with open(file, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f'Updated {file}')
        else:
            print(f'No search bar found or already updated in {file}')
    else:
        print(f'No add button found in {file}')
