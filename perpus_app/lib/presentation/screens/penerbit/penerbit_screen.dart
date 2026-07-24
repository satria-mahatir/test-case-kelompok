import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:perpus_app/presentation/providers/penerbit_provider.dart';

class PenerbitScreen extends StatefulWidget {
  final bool showAppBar;
  const PenerbitScreen({super.key, this.showAppBar = true});

  @override
  State<PenerbitScreen> createState() => _PenerbitScreenState();
}

class _PenerbitScreenState extends State<PenerbitScreen> {
  final TextEditingController _searchController = TextEditingController();
  Timer? _debounce;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchData();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    _debounce?.cancel();
    super.dispose();
  }

  Future<void> _fetchData() async {
    await context.read<PenerbitProvider>().fetchPenerbit(search: _searchController.text);
  }

  void _onSearchChanged(String query) {
    if (_debounce?.isActive ?? false) _debounce!.cancel();
    _debounce = Timer(const Duration(milliseconds: 400), () {
      _fetchData();
    });
  }

  void _showFormDialog({int? id, String? initialNama, String? initialKota}) {
    final namaController = TextEditingController(text: initialNama);
    final kotaController = TextEditingController(text: initialKota);
    final messenger = ScaffoldMessenger.of(context);
    final provider = context.read<PenerbitProvider>();
    showModalBottomSheet(
      context: context,
      backgroundColor: const Color(0xFF1A1A2E),
      isScrollControlled: true,
      builder: (ctx) => Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom, top: 20, left: 20, right: 20),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(id == null ? 'Tambah Penerbit' : 'Edit Penerbit', style: const TextStyle(color: Colors.white, fontSize: 18, fontFamily: 'Poppins')),
            const SizedBox(height: 16),
            TextField(
              controller: namaController,
              style: const TextStyle(color: Colors.white),
              decoration: InputDecoration(hintText: 'Nama Penerbit', hintStyle: const TextStyle(color: Colors.white54), filled: true, fillColor: const Color(0xFF0D0D1A), border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none)),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: kotaController,
              style: const TextStyle(color: Colors.white),
              decoration: InputDecoration(hintText: 'Kota', hintStyle: const TextStyle(color: Colors.white54), filled: true, fillColor: const Color(0xFF0D0D1A), border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none)),
            ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF8E2DE2)),
                onPressed: () async {
                  if (namaController.text.isEmpty) return;
                  Navigator.pop(ctx);
                  bool success;
                  if (id == null) {
                    success = await provider.createPenerbit(namaController.text, kotaController.text);
                  } else {
                    success = await provider.updatePenerbit(id, namaController.text, kotaController.text);
                  }
                  if (success) {
                    messenger.showSnackBar(const SnackBar(content: Text('Berhasil disimpan')));
                  } else {
                    messenger.showSnackBar(const SnackBar(content: Text('Gagal menyimpan')));
                  }
                },
                child: const Text('Simpan'),
              ),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0D0D1A),
      appBar: widget.showAppBar ? AppBar(
        backgroundColor: const Color(0xFF1A1A2E),
        title: const Text('Penerbit', style: TextStyle(fontFamily: 'Poppins')),
      ) : null,
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: TextField(
              controller: _searchController,
              style: const TextStyle(color: Colors.white),
              decoration: InputDecoration(
                hintText: 'Cari penerbit...',
                hintStyle: const TextStyle(color: Colors.white54),
                prefixIcon: const Icon(Icons.search, color: Colors.white54),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear, color: Colors.white54),
                        onPressed: () {
                          _searchController.clear();
                          _fetchData();
                        },
                      )
                    : null,
                filled: true,
                fillColor: const Color(0xFF1A1A2E),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
              ),
              onChanged: _onSearchChanged,
              onSubmitted: (_) => _fetchData(),
            ),
          ),
          Expanded(
            child: Consumer<PenerbitProvider>(
              builder: (context, provider, _) {
                if (provider.isLoading && provider.penerbitList.isEmpty) return const Center(child: CircularProgressIndicator(color: Color(0xFF8E2DE2)));
                
                if (provider.penerbitList.isEmpty) {
                  return const Center(child: Text('Tidak ada penerbit ditemukan', style: TextStyle(color: Colors.white70, fontFamily: 'Poppins')));
                }

                return RefreshIndicator(
                  onRefresh: _fetchData,
                  color: const Color(0xFF8E2DE2),
                  child: ListView.builder(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    itemCount: provider.penerbitList.length,
                    itemBuilder: (context, index) {
                      final p = provider.penerbitList[index];
                      return Card(
                        color: const Color(0xFF1A1A2E),
                        child: ListTile(
                          title: Text(p.namaPenerbit, style: const TextStyle(color: Colors.white, fontFamily: 'Poppins')),
                          subtitle: Text(p.kota ?? '-', style: const TextStyle(color: Colors.white54, fontFamily: 'Poppins', fontSize: 12)),
                          trailing: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              IconButton(
                                icon: const Icon(Icons.edit, color: Colors.white70),
                                onPressed: () => _showFormDialog(id: p.id, initialNama: p.namaPenerbit, initialKota: p.kota),
                              ),
                              IconButton(
                                icon: const Icon(Icons.delete, color: Colors.redAccent),
                                onPressed: () async {
                                  await context.read<PenerbitProvider>().deletePenerbit(p.id);
                                },
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        backgroundColor: const Color(0xFF8E2DE2),
        onPressed: () => _showFormDialog(),
        child: const Icon(Icons.add),
      ),
    );
  }
}
