class PenulisModel {
  final int id;
  final String namaPenulis;
  final String? email;
  final String? createdAt;
  final String? updatedAt;

  PenulisModel({
    required this.id,
    required this.namaPenulis,
    this.email,
    this.createdAt,
    this.updatedAt,
  });

  factory PenulisModel.fromJson(Map<String, dynamic> json) {
    return PenulisModel(
      id: json['id'],
      namaPenulis: json['nama_penulis'],
      email: json['email'],
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama_penulis': namaPenulis,
      'email': email,
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }

  PenulisModel copyWith({
    int? id,
    String? namaPenulis,
    String? email,
    String? createdAt,
    String? updatedAt,
  }) {
    return PenulisModel(
      id: id ?? this.id,
      namaPenulis: namaPenulis ?? this.namaPenulis,
      email: email ?? this.email,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
    );
  }
}
