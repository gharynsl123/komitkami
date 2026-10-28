import sys

with open('resources/views/inventory/pengecekan-material/detail-pengecekan.blade.php', 'r') as f:
    lines = f.readlines()

start_idx = -1
end_idx = -1

for i, line in enumerate(lines):
    if '<!-- Section 1: Form Pengambilan Sampel -->' in line:
        start_idx = i
        break

for i in range(len(lines)-1, -1, -1):
    if '<!-- Section 4: Laporan Inspeksi -->' in lines[i]:
        # we need to find the end of this section
        # actually, let's just find the closing </section> after Section 4
        for j in range(i, len(lines)):
            if '</section>' in lines[j]:
                end_idx = j
                break
        break

if start_idx != -1 and end_idx != -1:
    new_content = """    @if($inventory->type == 'Bahan Baku')
        @include('inventory.pengecekan-material.partials.inspeksi-penerimaan')
        @include('inventory.pengecekan-material.partials.pengambilan-sampel')
        @include('inventory.pengecekan-material.partials.laporan-inspeksi')
        @include('inventory.pengecekan-material.partials.pengujian')
    @else
        @include('inventory.pengecekan-material.partials.pengambilan-sampel')
        @include('inventory.pengecekan-material.partials.laporan-inspeksi')
        @include('inventory.pengecekan-material.partials.pengujian')
    @endif
"""
    lines[start_idx:end_idx+1] = [new_content]
    
    with open('resources/views/inventory/pengecekan-material/detail-pengecekan.blade.php', 'w') as f:
        f.writelines(lines)
    print("Replaced successfully")
else:
    print(f"Could not find markers. Start: {start_idx}, End: {end_idx}")

