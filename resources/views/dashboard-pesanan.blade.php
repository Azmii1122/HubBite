<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HubBite - Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased flex min-h-screen">

    <!-- Sidebar Admin -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between fixed h-full">
        <div>
            <div class="p-6">
                <h2 class="text-xl font-bold text-orange-500 tracking-tight">HubBite <span class="text-gray-800">Admin</span></h2>
                <p class="text-xs text-gray-400 mt-0.5">Central Management Control</p>
            </div>
            <nav class="mt-2 space-y-1">
                <a href="#" class="flex items-center px-6 py-3 text-sm font-medium text-gray-500 hover:bg-gray-50">Ringkasan Sistem</a>
                <a href="/dashboard-pesanan" class="flex items-center px-6 py-3 text-sm font-semibold text-orange-600 bg-orange-50/60 border-r-4 border-orange-500">Kelola Semua Pesanan</a>
                <a href="#" class="flex items-center px-6 py-3 text-sm font-medium text-gray-500 hover:bg-gray-50">Daftar Tenant / Seller</a>
                <a href="#" class="flex items-center px-6 py-3 text-sm font-medium text-gray-500 hover:bg-gray-50">Laporan Keuangan</a>
            </nav>
        </div>
        <div class="p-5 border-t border-gray-100">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">SA</div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Super Admin</p>
                    <p class="text-xs text-orange-600 font-medium">● Full Access Control</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1 ml-64 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin - Manajemen Pesanan</h1>
                <p class="text-sm text-gray-500 mt-1">Pantau transaksi pesanan lengkap dengan menu dan status dapur.</p>
            </div>
            <button onclick="openModal()" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition">+ Tambah Pesanan</button>
        </div>

        <!-- Tabel Pesanan -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">ID Order</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Menu / Barang</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Total Harga</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Aksi CRUD</th>
                        </tr>
                    </thead>
                    <tbody id="pesanan-table-body" class="bg-white divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Form -->
    <div id="pesananModal" class="fixed inset-0 bg-gray-900/50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
            <h2 id="modalTitle" class="text-lg font-bold text-gray-900 mb-4">Tambah Pesanan</h2>
            <form id="pesananForm" onsubmit="submitPesanan(event)">
                <input type="hidden" id="pesanan_id">
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Pelanggan</label>
                    <input type="text" id="customer_name" required class="w-full border border-gray-300 rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Menu / Barang</label>
                    <input type="text" id="item_name" required class="w-full border border-gray-300 rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Total Harga (Rp)</label>
                    <input type="number" id="total_price" required class="w-full border border-gray-300 rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm">Batal</button>
                    <button type="submit" class="bg-orange-500 text-white px-5 py-2 rounded-lg text-sm font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const apiUrl = '/api/pesanan';

        function fetchPesanan() {
            axios.get(apiUrl).then(response => {
                const data = response.data.data;
                const tbody = document.getElementById('pesanan-table-body');
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-6 text-center text-sm text-gray-400">Belum ada pesanan.</td></tr>`;
                    return;
                }
                data.forEach(item => {
                    let badge = item.status === 'selesai' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700';
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">#${item.order_id}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">${item.buyer_email || '-'}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${item.item_name || '-'}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Rp ${Number(item.total_amount || 0).toLocaleString('id-ID')}</td>
                            <td class="px-6 py-4"><span class="px-2.5 py-1 text-xs font-semibold rounded-full capitalize ${badge}">${item.status}</span></td>
                            <td class="px-6 py-4 text-center text-xs space-x-1">
                                <button onclick="editPesanan(${item.order_id}, '${item.buyer_email}', '${item.item_name}', ${item.total_amount})" class="bg-blue-50 text-blue-600 px-2 py-1 rounded">Edit</button>
                                <button onclick="updateStatus(${item.order_id}, 'diproses')" class="bg-orange-50 text-orange-600 px-2 py-1 rounded">Proses</button>
                                <button onclick="updateStatus(${item.order_id}, 'selesai')" class="bg-green-50 text-green-600 px-2 py-1 rounded">Selesai</button>
                                <button onclick="deletePesanan(${item.order_id})" class="bg-red-50 text-red-600 px-2 py-1 rounded">Hapus</button>
                            </td>
                        </tr>`;
                });
            });
        }

        function submitPesanan(e) {
            e.preventDefault();
            const id = document.getElementById('pesanan_id').value;
            const payload = {
                customer_name: document.getElementById('customer_name').value,
                item_name: document.getElementById('item_name').value,
                total_price: document.getElementById('total_price').value
            };
            if (id) {
                axios.put(`${apiUrl}/${id}`, payload).then(() => { closeModal(); fetchPesanan(); });
            } else {
                axios.post(apiUrl, payload).then(() => { closeModal(); fetchPesanan(); });
            }
        }

        function updateStatus(id, status) {
            axios.put(`${apiUrl}/${id}`, { status }).then(() => fetchPesanan());
        }

        function deletePesanan(id) {
            if (confirm('Hapus pesanan?')) {
                axios.delete(`${apiUrl}/${id}`).then(() => fetchPesanan());
            }
        }

        function openModal() {
            document.getElementById('pesanan_id').value = '';
            document.getElementById('modalTitle').innerText = 'Tambah Pesanan Baru';
            document.getElementById('pesananForm').reset();
            document.getElementById('pesananModal').classList.remove('hidden');
        }

        function editPesanan(id, buyer, item, amount) {
            document.getElementById('pesanan_id').value = id;
            document.getElementById('modalTitle').innerText = 'Edit Pesanan';
            document.getElementById('customer_name').value = buyer.replace('@hubbite.com', '');
            document.getElementById('item_name').value = item;
            document.getElementById('total_price').value = amount;
            document.getElementById('pesananModal').classList.remove('hidden');
        }

        function closeModal() { document.getElementById('pesananModal').classList.add('hidden'); }

        fetchPesanan();
    </script>
</body>
</html>