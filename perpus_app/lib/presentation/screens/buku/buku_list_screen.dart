import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:perpus_app/presentation/providers/buku_provider.dart';
import 'package:perpus_app/presentation/screens/buku/buku_detail_screen.dart';
import 'package:perpus_app/presentation/screens/buku/buku_form_screen.dart';
import 'package:perpus_app/data/models/buku_model.dart';

class BukuListScreen extends StatefulWidget {
  const BukuListScreen({super.key});

  @override
  State<BukuListScreen> createState() => _BukuListScreenState();
}

class _BukuListScreenState extends State<BukuListScreen> {
  final TextEditingController _searchController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  Timer? _debounce;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchData();
    });

    _scrollController.addListener(() {
      if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
        final provider = context.read<BukuProvider>();
        if (provider.hasMore && !provider.isLoadingMore) {
          provider.loadMoreBukus();
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
    await context.read<BukuProvider>().fetchBukus(search: _searchController.text);
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
        title: const Text('Daftar Buku', style: TextStyle(fontFamily: 'Poppins')),
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: TextField(
              controller: _searchController,
              style: const TextStyle(color: Colors.white),
              decoration: InputDecoration(
                hintText: 'Cari judul, ISBN, penulis...',
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
          Expanded(
            child: Consumer<BukuProvider>(
              builder: (context, provider, _) {
                if (provider.isLoading && provider.bukus.isEmpty) {
                  return const Center(child: CircularProgressIndicator(color: Color(0xFF8E2DE2)));
                }

                if (provider.error != null && provider.bukus.isEmpty) {
                  return Center(
                    child: Padding(
                      padding: const EdgeInsets.all(24.0),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.error_outline, size: 48, color: Colors.redAccent),
                          const SizedBox(height: 12),
                          Text(provider.error!, textAlign: TextAlign.center, style: const TextStyle(color: Colors.white70)),
                          const SizedBox(height: 16),
                          ElevatedButton(
                            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF8E2DE2)),
                            onPressed: _fetchData,
                            child: const Text('Coba Lagi'),
                          )
                        ],
                      ),
                    ),
                  );
                }

                if (provider.bukus.isEmpty) {
                  return const Center(
                    child: Text('Tidak ada buku ditemukan', style: TextStyle(color: Colors.white70, fontFamily: 'Poppins')),
                  );
                }

                return RefreshIndicator(
                  onRefresh: _fetchData,
                  color: const Color(0xFF8E2DE2),
                  child: GridView.builder(
                    controller: _scrollController,
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      childAspectRatio: 0.65,
                      crossAxisSpacing: 12,
                      mainAxisSpacing: 12,
                    ),
                    itemCount: provider.bukus.length + (provider.isLoadingMore ? 2 : 0),
                    itemBuilder: (context, index) {
                      if (index >= provider.bukus.length) {
                        return Container(
                          decoration: BoxDecoration(
                            color: const Color(0xFF1A1A2E),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Center(
                            child: SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF8E2DE2)),
                            ),
                          ),
                        );
                      }
                      final buku = provider.bukus[index];
                      return _buildBukuCard(buku);
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
          await Navigator.push(context, MaterialPageRoute(builder: (_) => const BukuFormScreen()));
          if (mounted) _fetchData();
        },
        child: const Icon(Icons.add),
      ),
    );
  }

  Widget _buildBukuCard(BukuModel buku) {
    bool inStock = buku.stok > 0;
    return GestureDetector(
      onTap: () async {
        await Navigator.push(context, MaterialPageRoute(builder: (_) => BukuDetailScreen(buku: buku)));
        if (mounted) _fetchData();
      },
      child: Container(
        decoration: BoxDecoration(
          color: const Color(0xFF1A1A2E),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.white10),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: Hero(
                tag: 'buku_${buku.id}',
                child: Container(
                  decoration: const BoxDecoration(
                    borderRadius: BorderRadius.vertical(top: Radius.circular(12)),
                    gradient: LinearGradient(
                      colors: [Color(0xFF3F2B96), Color(0xFF0D0D1A)],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                  ),
                  child: Center(
                    child: Icon(Icons.book, size: 64, color: Colors.white.withValues(alpha: 0.5)),
                  ),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(12.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    buku.judul,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontFamily: 'Poppins', fontSize: 14),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    buku.penulis?.namaPenulis ?? 'Unknown Author',
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(color: Colors.white54, fontSize: 12, fontFamily: 'Poppins'),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: const Color(0xFF8E2DE2).withValues(alpha: 0.2),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          buku.kategori?.namaKategori ?? 'Umum',
                          style: const TextStyle(color: Color(0xFF8E2DE2), fontSize: 10, fontFamily: 'Poppins'),
                        ),
                      ),
                      Text(
                        'Stok: ${buku.stok}',
                        style: TextStyle(
                          color: inStock ? Colors.greenAccent : Colors.redAccent,
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          fontFamily: 'Poppins'
                        ),
                      ),
                    ],
                  )
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
