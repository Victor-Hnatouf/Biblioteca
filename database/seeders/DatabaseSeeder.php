<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Editora;
use App\Models\Autor;
use App\Models\Livro;
use App\Models\Room;
use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Seed Users (with avatars and status matching Campfire photos)
        $admin = User::create([
            'name' => 'Matthew Rogerson',
            'email' => 'admin@example.com',
            'password' => 'password', // Auto hashed by Cast or model
            'role' => User::ROLE_ADMIN,
            'estado' => 'A trabalhar a partir de casa 🏡',
        ]);

        $cidadao = User::create([
            'name' => 'Cidadão',
            'email' => 'cidadao@example.com',
            'password' => 'password',
            'role' => User::ROLE_CIDADAO,
            'estado' => 'Disponível',
        ]);

        $luna = User::create([
            'name' => 'Luna Rodriguez',
            'email' => 'luna@example.com',
            'password' => 'password',
            'role' => User::ROLE_CIDADAO,
            'estado' => 'Focada em design 🎨',
        ]);

        $jason = User::create([
            'name' => 'Jason Fried',
            'email' => 'jason@example.com',
            'password' => 'password',
            'role' => User::ROLE_CIDADAO,
            'estado' => 'A ler um livro 📖',
        ]);

        $alisha = User::create([
            'name' => 'Alisha Munir',
            'email' => 'alisha@example.com',
            'password' => 'password',
            'role' => User::ROLE_CIDADAO,
            'estado' => 'Em reunião 💼',
        ]);

        $sofia = User::create([
            'name' => 'Sofia Castillo Rivera',
            'email' => 'sofia@example.com',
            'password' => 'password',
            'role' => User::ROLE_CIDADAO,
            'estado' => 'Bom dia! ☀️',
        ]);

        $leah = User::create([
            'name' => 'Leah Bernstein',
            'email' => 'leah@example.com',
            'password' => 'password',
            'role' => User::ROLE_CIDADAO,
            'estado' => 'A desenhar esquemas 💻',
        ]);

        // 2. Seed Library Catalog Data (Editors, Authors, Books)
        $editora1 = Editora::create(['nome' => 'Editora Almedina']);
        $editora2 = Editora::create(['nome' => 'Porto Editora']);

        $autor1 = Autor::create(['nome' => 'José Saramago']);
        $autor2 = Autor::create(['nome' => 'Fernando Pessoa']);
        $autor3 = Autor::create(['nome' => 'Luís de Camões']);

        $livro1 = Livro::create([
            'isbn' => '978-972-0-04958-3',
            'nome' => 'Ensaio sobre a Cegueira',
            'editora_id' => $editora1->id,
            'bibliografia' => 'Uma das obras mais célebres de José Saramago.',
            'preco' => '16.50',
        ]);
        $livro1->autores()->attach($autor1->id);

        $livro2 = Livro::create([
            'isbn' => '978-972-0-30154-4',
            'nome' => 'Mensagem',
            'editora_id' => $editora2->id,
            'bibliografia' => 'O único livro de poesia em português publicado em vida por Fernando Pessoa.',
            'preco' => '12.00',
        ]);
        $livro2->autores()->attach($autor2->id);

        $livro3 = Livro::create([
            'isbn' => '978-972-0-31254-0',
            'nome' => 'Os Lusíadas',
            'editora_id' => $editora2->id,
            'bibliografia' => 'A grande obra épica da literatura portuguesa.',
            'preco' => '14.90',
        ]);
        $livro3->autores()->attach($autor3->id);

        // 3. Seed Chat Rooms (inspired by Campfire screenshots)
        $allTalk = Room::create(['nome' => 'All Talk', 'is_dm' => false, 'created_by' => $admin->id]);
        $design = Room::create(['nome' => 'Design 🎨', 'is_dm' => false, 'created_by' => $admin->id]);
        $travel = Room::create(['nome' => 'Travel ✈️', 'is_dm' => false, 'created_by' => $admin->id]);
        $marketing = Room::create(['nome' => 'Marketing 📈', 'is_dm' => false, 'created_by' => $admin->id]);
        $adminRoom = Room::create(['nome' => 'Admin 📁', 'is_dm' => false, 'created_by' => $admin->id]);
        $hr = Room::create(['nome' => 'HR 😊', 'is_dm' => false, 'created_by' => $admin->id]);

        // Attach seeded users to rooms
        $allUsers = [$admin->id, $cidadao->id, $luna->id, $jason->id, $alisha->id, $sofia->id, $leah->id];
        $allTalk->users()->attach($allUsers);
        $design->users()->attach([$admin->id, $luna->id, $leah->id]);
        $marketing->users()->attach([$admin->id, $alisha->id]);
        $adminRoom->users()->attach([$admin->id]);

        // 4. Seed Messages in "All Talk" (Matching the screenshots precisely)
        // Dec 12, 2023
        $dec12 = now()->subDays(10)->setTime(7, 4, 0);
        Message::create([
            'room_id' => $allTalk->id,
            'user_id' => $sofia->id,
            'conteudo' => 'Good morning! 👋',
            'created_at' => $dec12,
            'updated_at' => $dec12,
        ]);

        $dec12_2 = now()->subDays(10)->setTime(7, 29, 0);
        Message::create([
            'room_id' => $allTalk->id,
            'user_id' => $luna->id,
            'conteudo' => 'morning!',
            'created_at' => $dec12_2,
            'updated_at' => $dec12_2,
        ]);

        $dec12_3 = now()->subDays(10)->setTime(7, 31, 0);
        Message::create([
            'room_id' => $allTalk->id,
            'user_id' => $alisha->id,
            'conteudo' => 'Just a reminder that our company meeting is later this morning at 11:30am!',
            'created_at' => $dec12_3,
            'updated_at' => $dec12_3,
        ]);

        // Dec 21, 2023
        $dec21 = now()->subDays(1)->setTime(13, 16, 0);
        Message::create([
            'room_id' => $allTalk->id,
            'user_id' => $admin->id, // Matthew
            'conteudo' => 'This is Campfire 🔥',
            'created_at' => $dec21,
            'updated_at' => $dec21,
        ]);

        Message::create([
            'room_id' => $allTalk->id,
            'user_id' => $leah->id,
            'conteudo' => "It's a wonderfully simple and straightforward group chat tool!",
            'created_at' => $dec21->addSeconds(30),
            'updated_at' => $dec21,
        ]);

        Message::create([
            'room_id' => $allTalk->id,
            'user_id' => $admin->id,
            'conteudo' => 'You install it on your own server.',
            'created_at' => $dec21->addSeconds(60),
            'updated_at' => $dec21,
        ]);

        Message::create([
            'room_id' => $allTalk->id,
            'user_id' => $leah->id,
            'conteudo' => "Let's go over how it works...",
            'created_at' => $dec21->addSeconds(90),
            'updated_at' => $dec21,
        ]);
    }
}
