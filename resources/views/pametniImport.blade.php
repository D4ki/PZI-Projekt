<x-layout>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://unpkg.com/feather-icons"></script>
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <!-- CSV parser -->
        <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
        <!-- Excel parser -->
        <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    </head>
    <body class="bg-gray-50">
        <div class="min-h-screen flex flex-col">
            <!-- Header -->
            <x-page-heading>Pametni Import</x-page-heading>

            <x-forms.divider></x-forms.divider>

            <!-- Main Content -->
            <main class="flex-grow">
                <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8" data-aos="fade-up">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-800">Import Data</h2>
                        </div>

                        <!-- Dropzone -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center mb-6" id="dropzone">
                            <div class="flex flex-col items-center justify-center">
                                <i data-feather="upload-cloud" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-700 mb-2">Drag and drop files here</h3>
                                <p class="text-gray-500 mb-4">or</p>
                                <label for="file-upload" class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                                    <span>Browse Files</span>
                                    <input id="file-upload" name="file-upload" type="file" class="sr-only" accept=".csv,.xlsx,.xls">
                                </label>
                                <p class="text-xs text-gray-500 mt-3">Supports CSV, XLSX (Excel) files up to 10MB</p>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="hidden" id="preview-section">
                            <h3 class="text-lg font-medium text-gray-700 mb-4">File Preview</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr id="preview-headers"></tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="preview-rows"></tbody>
                                </table>
                            </div>

                            <!-- Field Mapping -->
                            <div class="mt-8">
                                <h3 class="text-lg font-medium text-gray-700 mb-4">Field Mapping</h3>
                                <p class="text-gray-600 mb-4">Our system will automatically detect and map your columns to our database fields. You can adjust the mappings below if needed.</p>
                                
                                <div class="space-y-4" id="mapping-container"></div>

                                <div class="mt-6 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <input id="skip-header" name="skip-header" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" checked>
                                        <label for="skip-header" class="ml-2 block text-sm text-gray-700">First row contains headers</label>
                                    </div>
                                    
                                    <div class="flex space-x-3">
                                        <a href="/pametniImport">
                                        <button class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">Cancel</button></a>
                                        <button id="download-btn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition flex items-center">
                                            Download Processed
                                        </button>
                                        <button id="import-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center">
                                            Import Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <script>
            AOS.init();
            feather.replace();

            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('file-upload');
            const previewSection = document.getElementById('preview-section');
            const previewHeaders = document.getElementById('preview-headers');
            const previewRows = document.getElementById('preview-rows');
            const mappingContainer = document.getElementById('mapping-container');

            let uploadedHeaders = [];
            let uploadedRows = [];

            // Prevent defaults
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => dropzone.classList.add('border-indigo-500', 'bg-indigo-50'), false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => dropzone.classList.remove('border-indigo-500', 'bg-indigo-50'), false);
            });

            dropzone.addEventListener('drop', handleDrop, false);
            fileInput.addEventListener('change', handleFiles, false);

            function handleDrop(e) {
                handleFiles({ target: { files: e.dataTransfer.files } });
            }

            function handleFiles(e) {
                const files = e.target.files;
                if (files.length) {
                    const file = files[0];

                    if (file.name.endsWith(".csv")) {
                        Papa.parse(file, {
                            header: true,
                            skipEmptyLines: true,
                            complete: function(results) {
                                showPreview(results.meta.fields, results.data.slice(0, 10));
                            }
                        });
                    } else if (file.name.endsWith(".xlsx") || file.name.endsWith(".xls")) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const data = new Uint8Array(e.target.result);
                            const workbook = XLSX.read(data, { type: "array" });
                            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                            const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                            const headers = jsonData[0];
                            const rows = jsonData.slice(1, 11).map(rowArr => {
                                const obj = {};
                                headers.forEach((h, i) => { obj[h] = rowArr[i] || ""; });
                                return obj;
                            });
                            showPreview(headers, rows);
                        };
                        reader.readAsArrayBuffer(file);
                    } else {
                        alert("Podržani formati: CSV, XLSX, XLS");
                    }
                }
            }

            function showPreview(headers, rows) {
                uploadedHeaders = headers;
                uploadedRows = rows;

                previewHeaders.innerHTML = '';
                headers.forEach(header => {
                    const th = document.createElement('th');
                    th.className = 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider';
                    th.textContent = header;
                    previewHeaders.appendChild(th);
                });

                previewRows.innerHTML = '';
                rows.forEach(row => {
                    const tr = document.createElement('tr');
                    headers.forEach(header => {
                        const td = document.createElement('td');
                        td.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-500';
                        td.textContent = row[header] ?? '';
                        tr.appendChild(td);
                    });
                    previewRows.appendChild(tr);
                });

                // Mapping
                mappingContainer.innerHTML = '';
                const dbFields = ['title', 'name', 'salary', 'email', 'phone', 'address', 'location', 'employer', 'url', 'company', 'schedule', 'tags', 'ignore'];
                headers.forEach((header, index) => {
                    const div = document.createElement('div');
                    div.className = 'grid grid-cols-3 gap-4 items-center';
                    
                    const label = document.createElement('label');
                    label.className = 'text-sm font-medium text-gray-700';
                    label.textContent = `Column ${index + 1}: ${header}`;
                    label.htmlFor = `field-${index}`;
                    
                    const select = document.createElement('select');
                    select.id = `field-${index}`;
                    select.name = `field-${index}`;
                    select.className = 'mt-1 block w-full pl-3 pr-10 py-2 text-gray-700 border border-gray focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md';
                    
                    dbFields.forEach(field => {
                        const option = document.createElement('option');
                        option.value = field;
                        option.textContent = field.replace('_', ' ');
                        if (header.toLowerCase().includes(field)) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });

                    const detected = document.createElement('div');
                    detected.className = 'text-sm text-gray-500';
                    detected.textContent = 'Detected as: ' + (select.options[select.selectedIndex]?.text || 'Unknown');
                    
                    select.addEventListener("change", () => {
                        detected.textContent = "Detected as: " + select.options[select.selectedIndex].text;
                    });
                    
                    div.appendChild(label);
                    div.appendChild(select);
                    div.appendChild(detected);
                    mappingContainer.appendChild(div);
                });

                previewSection.classList.remove('hidden');
                dropzone.classList.add('hidden');
            }

            // Download mapped data
            document.getElementById("download-btn").addEventListener("click", () => {
                if (!uploadedHeaders.length || !uploadedRows.length) {
                    alert("Nema podataka za preuzimanje!");
                    return;
                }

                const mappedHeaders = [];
                const activeSelects = [];

                uploadedHeaders.forEach((h, i) => {
                    const selected = document.getElementById(`field-${i}`).value;
                    if (selected !== "ignore") {
                        mappedHeaders.push(selected);
                        activeSelects.push({ index: i, field: selected });
                    }
                });

                const newRows = uploadedRows.map(row => {
                    const obj = {};
                    activeSelects.forEach(sel => {
                        obj[sel.field] = row[uploadedHeaders[sel.index]] || "";
                    });
                    return obj;
                });

                const wsData = [mappedHeaders, ...newRows.map(r => mappedHeaders.map(h => r[h]))];
                const ws = XLSX.utils.aoa_to_sheet(wsData);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Processed");
                XLSX.writeFile(wb, "processed_data.xlsx");
            });

            //import podataka
            document.getElementById("import-btn").addEventListener("click", () => {
                if (!uploadedHeaders.length || !uploadedRows.length) {
                    alert("Nema podataka za import!");
                    return;
                }

                const mappedHeaders = [];
                const activeSelects = [];

                uploadedHeaders.forEach((h, i) => {
                    const selected = document.getElementById(`field-${i}`).value;
                    if (selected !== "ignore") {
                        mappedHeaders.push(selected);
                        activeSelects.push({ index: i, field: selected });
                    }
                });

                const newRows = uploadedRows.map(row => {
                    const obj = {};
                    activeSelects.forEach(sel => {
                        obj[sel.field] = row[uploadedHeaders[sel.index]] || "";
                    });
                    return obj;
                });

                fetch("/jobs/import", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ jobs: newRows })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("❌ Greška kod importa!");
                });
            });
        </script>
    </body>
</x-layout>