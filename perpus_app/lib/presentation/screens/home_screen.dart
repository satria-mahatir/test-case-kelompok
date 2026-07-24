import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:perpus_app/presentation/screens/buku/buku_list_screen.dart';
import 'package:perpus_app/presentation/screens/peminjaman/peminjaman_list_screen.dart';
import 'package:perpus_app/presentation/screens/kategori/kategori_screen.dart';
import 'package:perpus_app/presentation/screens/penulis/penulis_screen.dart';
import 'package:perpus_app/presentation/screens/penerbit/penerbit_screen.dart';
import 'package:perpus_app/presentation/providers/buku_provider.dart';
import 'package:perpus_app/presentation/providers/peminjaman_provider.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  Widget build(BuildContext context) {
    return const HomeDashboardScreen();
  }
}

class HomeDashboardScreen extends StatefulWidget {
  const HomeDashboardScreen({super.key});

  @override
  State<HomeDashboardScreen> createState() => _HomeDashboardScreenState();
}

class _HomeDashboardScreenState extends State<HomeDashboardScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<BukuProvider>().fetchBukus();
      context.read<PeminjamanProvider>().fetchPeminjaman(status: 'dipinjam');
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0D0D1A),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1A1A2E),
        elevation: 0,
        title: const Text('Perpustakaan Solusindo', style: TextStyle(fontFamily: 'Poppins', fontWeight: FontWeight.bold)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Selamat Datang, Petugas!',
              style: TextStyle(
                fontFamily: 'Poppins',
                fontSize: 20,
                fontWeight: FontWeight.w600,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 20),
            Row(
              children: [
                Expanded(
                  child: _buildStatCard('Total Buku', context.watch<BukuProvider>().total.toString(), [const Color(0xFF8E2DE2), const Color(0xFF4A00E0)]),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildStatCard('Peminjaman Aktif', context.watch<PeminjamanProvider>().total.toString(), [const Color(0xFFf12711), const Color(0xFFf5af19)]),
                ),
              ],
            ),
            const SizedBox(height: 24),
            const Text(
              'Quick Actions',
              style: TextStyle(
                fontFamily: 'Poppins',
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 12),
            GridView.count(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              crossAxisCount: 3,
              mainAxisSpacing: 12,
              crossAxisSpacing: 12,
              children: [
                _buildActionCard(context, 'Daftar Buku', Icons.menu_book, const BukuListScreen()),
                _buildActionCard(context, 'Peminjaman', Icons.assignment_turned_in, const PeminjamanListScreen()),
                _buildActionCard(context, 'Kategori', Icons.category, const KategoriScreen()),
                _buildActionCard(context, 'Penulis', Icons.person, const PenulisScreen()),
                _buildActionCard(context, 'Penerbit', Icons.business, const PenerbitScreen()),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatCard(String title, String count, List<Color> gradient) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        gradient: LinearGradient(colors: gradient),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(color: Colors.white70, fontFamily: 'Poppins')),
          const SizedBox(height: 8),
          Text(count, style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold, fontFamily: 'Poppins')),
        ],
      ),
    );
  }

  Widget _buildActionCard(BuildContext context, String title, IconData icon, Widget screen) {
    return InkWell(
      onTap: () async {
        await Navigator.push(context, MaterialPageRoute(builder: (_) => screen));
        if (context.mounted) {
          context.read<BukuProvider>().fetchBukus();
          context.read<PeminjamanProvider>().fetchPeminjaman(status: 'dipinjam');
        }
      },
      borderRadius: BorderRadius.circular(16),
      child: Container(
        decoration: BoxDecoration(
          color: const Color(0xFF1A1A2E),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: Colors.white10),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: Colors.white, size: 32),
            const SizedBox(height: 8),
            Text(title, textAlign: TextAlign.center, style: const TextStyle(color: Colors.white70, fontSize: 12, fontFamily: 'Poppins')),
          ],
        ),
      ),
    );
  }
}

class MasterDataPage extends StatelessWidget {
  const MasterDataPage({super.key});

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 3,
      child: Scaffold(
        backgroundColor: const Color(0xFF0D0D1A),
        appBar: AppBar(
          backgroundColor: const Color(0xFF1A1A2E),
          title: const Text('Master Data', style: TextStyle(fontFamily: 'Poppins')),
          bottom: const TabBar(
            indicatorColor: Color(0xFF8E2DE2),
            labelColor: Color(0xFF8E2DE2),
            unselectedLabelColor: Colors.white54,
            tabs: [
              Tab(text: 'Kategori'),
              Tab(text: 'Penulis'),
              Tab(text: 'Penerbit'),
            ],
          ),
        ),
        body: const TabBarView(
          children: [
            KategoriScreen(showAppBar: false),
            PenulisScreen(showAppBar: false),
            PenerbitScreen(showAppBar: false),
          ],
        ),
      ),
    );
  }
}
