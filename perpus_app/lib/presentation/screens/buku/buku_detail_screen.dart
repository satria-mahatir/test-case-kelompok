import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:perpus_app/data/models/buku_model.dart';
import 'package:perpus_app/presentation/providers/buku_provider.dart';
import 'package:perpus_app/presentation/screens/buku/buku_form_screen.dart';
import 'package:perpus_app/presentation/screens/peminjaman/peminjaman_form_screen.dart';

class BukuDetailScreen extends StatelessWidget {
  final BukuModel buku;

  const BukuDetailScreen({super.key, required this.buku});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0D0D1A),
      body: CustomScrollView(
        slivers: [
          SliverAppBar(
            expandedHeight: 300,
            pinned: true,
            backgroundColor: const Color(0xFF1A1A2E),
            flexibleSpace: FlexibleSpaceBar(
              background: Hero(
                tag: 'buku_${buku.id}',
                child: Container(
                  decoration: const BoxDecoration(
                    gradient: LinearGradient(
                      colors: [Color(0xFF8E2DE2), Color(0xFF4A00E0)],
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                    ),
                  ),
                  child: const Center(
                    child: Icon(Icons.menu_book, size: 100, color: Colors.white54),
                  ),
                ),
              ),
            ),
            actions: [
              IconButton(
                icon: const Icon(Icons.edit),
                onPressed: () {
                  Navigator.push(context, MaterialPageRoute(builder: (_) => BukuFormScreen(buku: buku)));
                },
              ),
              IconButton(
                icon: const Icon(Icons.delete, color: Colors.redAccent),
                onPressed: () => _confirmDelete(context),
              ),
            ],
          ),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(20.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    buku.judul,
                    style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold, fontFamily: 'Poppins'),
                  ),
                  const SizedBox(height: 16),
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: [
                      _buildChip('ISBN', buku.isbn ?? '-'),
                      _buildChip('Tahun', buku.tahunTerbit?.toString() ?? '-'),
                      _buildChip('Stok', buku.stok.toString(), color: buku.stok > 0 ? Colors.green : Colors.red),
                    ],
                  ),
                  const SizedBox(height: 24),
                  _buildInfoRow('Kategori', buku.kategori?.namaKategori ?? '-'),
                  const SizedBox(height: 12),
                  _buildInfoRow('Penulis', buku.penulis?.namaPenulis ?? '-'),
                  const SizedBox(height: 12),
                  _buildInfoRow('Penerbit', buku.penerbit?.namaPenerbit ?? '-'),
                  const SizedBox(height: 24),
                  const Text('Deskripsi', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold, fontFamily: 'Poppins')),
                  const SizedBox(height: 8),
                  Text(
                    buku.deskripsi ?? 'Tidak ada deskripsi',
                    style: const TextStyle(color: Colors.white70, fontSize: 14, fontFamily: 'Poppins', height: 1.5),
                  ),
                  const SizedBox(height: 40),
                  SizedBox(
                    width: double.infinity,
                    height: 50,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF8E2DE2),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: buku.stok > 0 ? () {
                        Navigator.push(context, MaterialPageRoute(builder: (_) => PeminjamanFormScreen(buku: buku)));
                      } : null,
                      child: const Text('Pinjamkan Buku', style: TextStyle(fontFamily: 'Poppins', fontSize: 16, fontWeight: FontWeight.bold)),
                    ),
                  ),
                  const SizedBox(height: 40),
                ],
              ),
            ),
          )
        ],
      ),
    );
  }

  Widget _buildChip(String label, String value, {Color? color}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: const Color(0xFF1A1A2E),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color ?? Colors.white24),
      ),
      child: Text('$label: $value', style: TextStyle(color: color ?? Colors.white70, fontSize: 12, fontFamily: 'Poppins')),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(width: 100, child: Text(label, style: const TextStyle(color: Colors.white54, fontFamily: 'Poppins'))),
        Expanded(child: Text(value, style: const TextStyle(color: Colors.white, fontFamily: 'Poppins', fontWeight: FontWeight.w500))),
      ],
    );
  }

  void _confirmDelete(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: const Color(0xFF1A1A2E),
        title: const Text('Hapus Buku', style: TextStyle(color: Colors.white, fontFamily: 'Poppins')),
        content: const Text('Yakin ingin menghapus buku ini?', style: TextStyle(color: Colors.white70, fontFamily: 'Poppins')),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal', style: TextStyle(color: Colors.white54))),
          TextButton(
            onPressed: () async {
              final messenger = ScaffoldMessenger.of(context);
              final navigator = Navigator.of(context);
              final provider = context.read<BukuProvider>();
              Navigator.pop(ctx);
              final success = await provider.deleteBuku(buku.id);
              if (success) {
                messenger.showSnackBar(const SnackBar(content: Text('Buku berhasil dihapus')));
                navigator.pop(); // go back to list
              } else {
                messenger.showSnackBar(SnackBar(content: Text(provider.error ?? 'Gagal menghapus')));
              }
            },
            child: const Text('Hapus', style: TextStyle(color: Colors.redAccent)),
          ),
        ],
      ),
    );
  }
}
