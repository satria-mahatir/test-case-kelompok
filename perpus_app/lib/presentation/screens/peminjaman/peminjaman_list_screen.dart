import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:perpus_app/presentation/providers/peminjaman_provider.dart';
import 'package:perpus_app/presentation/providers/buku_provider.dart';
import 'package:perpus_app/presentation/screens/peminjaman/peminjaman_form_screen.dart';

class PeminjamanListScreen extends StatefulWidget {
  const PeminjamanListScreen({super.key});

  @override
  State<PeminjamanListScreen> createState() => _PeminjamanListScreenState();
}

class _PeminjamanListScreenState extends State<PeminjamanListScreen> {
  final TextEditingController _searchController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  String _selectedStatus = '';
  Timer? _debounce;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchData();
    });

    _scrollController.addListener(() {
      if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
        final provider = context.read<PeminjamanProvider>();
        if (provider.hasMore && !provider.isLoadingMore) {
          provider.loadMorePeminjaman();
        }
      }
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    _scrollController.dispose();
    _debounce?.cancel();
    super.dispose();
  }

  Future<void> _fetchData() async {
    await context.read<PeminjamanProvider>().fetchPeminjaman(
          search: _searchController.text,
          status: _selectedStatus.isEmpty ? null : _selectedStatus,
        );
  }

  void _onSearchChanged(String query) {
    if (_debounce?.isActive ?? false) _debounce!.cancel();
    _debounce = Timer(const Duration(milliseconds: 400), () {
      _fetchData();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0D0D1A),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1A1A2E),
        title: const Text('Data Peminjaman', style: TextStyle(fontFamily: 'Poppins')),
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
            child: TextField(
              controller: _searchController,
              style: const TextStyle(color: Colors.white),
              decoration: InputDecoration(
                hintText: 'Cari NIS, Nama Peminjam...',
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
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide.none,
                ),
              ),
              onChanged: _onSearchChanged,
              onSubmitted: (_) => _fetchData(),
            ),
          ),
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: Row(
              children: [
                _buildFilterChip('Semua', ''),
                const SizedBox(width: 8),
                _buildFilterChip('Dipinjam', 'dipinjam'),
                const SizedBox(width: 8),
                _buildFilterChip('Dikembalikan', 'dikembalikan'),
                const SizedBox(width: 8),
                _buildFilterChip('Terlambat', 'terlambat'),
              ],
            ),
          ),
          Expanded(
            child: Consumer<PeminjamanProvider>(
              builder: (context, provider, _) {
                if (provider.isLoading && provider.peminjamanList.isEmpty) {
                  return const Center(child: CircularProgressIndicator(color: Color(0xFF8E2DE2)));
                }

                if (provider.peminjamanList.isEmpty) {
                  return const Center(
                    child: Text('Tidak ada data peminjaman', style: TextStyle(color: Colors.white70, fontFamily: 'Poppins')),
                  );
                }

                return RefreshIndicator(
                  onRefresh: _fetchData,
                  color: const Color(0xFF8E2DE2),
                  child: ListView.separated(
                    controller: _scrollController,
                    padding: const EdgeInsets.all(16),
                    itemCount: provider.peminjamanList.length + (provider.isLoadingMore ? 1 : 0),
                    separatorBuilder: (_, __) => const SizedBox(height: 12),
                    itemBuilder: (context, index) {
                      if (index >= provider.peminjamanList.length) {
                        return const Center(
                          child: Padding(
                            padding: EdgeInsets.all(12.0),
                            child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF8E2DE2)),
                          ),
                        );
                      }
                      final p = provider.peminjamanList[index];
                      return Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: const Color(0xFF1A1A2E),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.white10),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Expanded(
                                  child: Text(
                                    p.buku?.judul ?? 'Buku tidak diketahui',
                                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontFamily: 'Poppins', fontSize: 15),
                                  ),
                                ),
                                _buildStatusBadge(p.status),
                              ],
                            ),
                            const SizedBox(height: 6),
                            Wrap(
                              spacing: 8,
                              runSpacing: 4,
                              children: [
                                if (p.buku?.kategori != null)
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                    decoration: BoxDecoration(color: const Color(0xFF8E2DE2).withValues(alpha: 0.2), borderRadius: BorderRadius.circular(4)),
                                    child: Text(p.buku!.kategori!.namaKategori, style: const TextStyle(color: Color(0xFF8E2DE2), fontSize: 10, fontFamily: 'Poppins')),
                                  ),
                                if (p.buku?.penulis != null)
                                  Text('• Penulis: ${p.buku!.penulis!.namaPenulis}', style: const TextStyle(color: Colors.white60, fontSize: 11, fontFamily: 'Poppins')),
                                if (p.buku?.penerbit != null)
                                  Text('• Penerbit: ${p.buku!.penerbit!.namaPenerbit}', style: const TextStyle(color: Colors.white60, fontSize: 11, fontFamily: 'Poppins')),
                              ],
                            ),
                            const SizedBox(height: 8),
                            Text('Peminjam: ${p.namaPeminjam} (${p.nis})', style: const TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w500, fontFamily: 'Poppins')),
                            const SizedBox(height: 4),
                            Text('Pinjam: ${p.tanggalPinjam}  |  Kembali: ${p.tanggalKembaliRencana}', style: const TextStyle(color: Colors.white54, fontSize: 11, fontFamily: 'Poppins')),
                            if (p.status == 'dipinjam' || p.status == 'terlambat') ...[
                              const SizedBox(height: 12),
                              Align(
                                alignment: Alignment.centerRight,
                                child: ElevatedButton.icon(
                                  style: ElevatedButton.styleFrom(backgroundColor: Colors.green, padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8)),
                                  icon: const Icon(Icons.assignment_return, size: 16),
                                  label: const Text('Kembalikan Buku', style: TextStyle(fontFamily: 'Poppins', fontSize: 12)),
                                  onPressed: () => _confirmKembalikan(context, p.id),
                                ),
                              )
                            ]
                          ],
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
        onPressed: () async {
          await Navigator.push(context, MaterialPageRoute(builder: (_) => const PeminjamanFormScreen()));
          if (mounted) _fetchData();
        },
        child: const Icon(Icons.add),
      ),
    );
  }

  Widget _buildFilterChip(String label, String value) {
    bool isSelected = _selectedStatus == value;
    return ChoiceChip(
      label: Text(label, style: TextStyle(color: isSelected ? Colors.white : Colors.white70, fontFamily: 'Poppins')),
      selected: isSelected,
      selectedColor: const Color(0xFF8E2DE2),
      backgroundColor: const Color(0xFF1A1A2E),
      onSelected: (selected) {
        if (selected) {
          setState(() => _selectedStatus = value);
          _fetchData();
        }
      },
    );
  }

  Widget _buildStatusBadge(String status) {
    Color color;
    if (status == 'dipinjam') {
      color = Colors.amber;
    } else if (status == 'dikembalikan') {
      color = Colors.green;
    } else {
      color = Colors.redAccent;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: color.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(8)),
      child: Text(status.toUpperCase(), style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.bold, fontFamily: 'Poppins')),
    );
  }

  void _confirmKembalikan(BuildContext context, int id) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: const Color(0xFF1A1A2E),
        title: const Text('Kembalikan Buku', style: TextStyle(color: Colors.white, fontFamily: 'Poppins')),
        content: const Text('Konfirmasi pengembalian buku ini?', style: TextStyle(color: Colors.white70, fontFamily: 'Poppins')),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal', style: TextStyle(color: Colors.white54))),
          TextButton(
            onPressed: () async {
              final messenger = ScaffoldMessenger.of(context);
              final provider = context.read<PeminjamanProvider>();
              final bukuProvider = context.read<BukuProvider>();
              Navigator.pop(ctx);
              final success = await provider.kembalikanBuku(id, bukuProvider: bukuProvider);
              if (success) {
                messenger.showSnackBar(const SnackBar(content: Text('Buku berhasil dikembalikan')));
              } else {
                messenger.showSnackBar(SnackBar(content: Text(provider.error ?? 'Gagal')));
              }
            },
            child: const Text('Ya, Kembalikan', style: TextStyle(color: Colors.greenAccent)),
          ),
        ],
      ),
    );
  }
}
