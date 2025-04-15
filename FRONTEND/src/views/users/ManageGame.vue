<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

const title = ref('');
const description = ref('');
const successMessage = ref('');
const errorMessage = ref('');
const games = ref([]);
const error = ref('');

const addGame = async () => {
  try {
    const response = await axios.post('/games', {
      title: title.value,
      description: description.value,
    });
    

    successMessage.value = 'Game berhasil ditambahkan!';
    errorMessage.value = '';
    title.value = '';
    description.value = '';

    // Refresh daftar game
    await fetchGame();

    console.log(response.data);
  } catch (err) {
    successMessage.value = '';
    errorMessage.value = 'Gagal menambahkan game.';
    console.error(err);
  }
};

const fetchGame = async () => {
  try {
    const response = await axios.get('/games');
    games.value = response.data.content;
    error.value = '';
    console.log(response.data);
  } catch (err) {
    error.value = err.response?.data?.message || 'Gagal mengambil data game.';
  }
};

const deleteGame = async (id) => {
  if (!confirm('Yakin ingin menghapus game ini?')) return;
  try {
    await axios.delete(`/games/${id}`);
    successMessage.value = 'Game berhasil dihapus!';
    errorMessage.value = '';

    // Refresh daftar game
    await fetchGame();
  } catch (err) {
    errorMessage.value = 'Gagal menghapus game.';
    console.error(err);
  }
};

onMounted(fetchGame);
</script>

<template>
  <div class="container">
    <div class="form-container">
      <h1>Manage Game</h1>
      <p style="color: red;">{{ errorMessage }}</p>
      <p style="color: green;">{{ successMessage }}</p>

      <div class="form-input">
        <input type="text" v-model="title" placeholder="Title Game" />
        <input type="text" v-model="description" placeholder="Description Game" />
        <button @click="addGame">Add Game</button>
      </div>
    </div>

    <div class="game-list">
      <h2>Daftar Game</h2>
      <table border="1" cellpadding="10">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Slug</th>
            <th>Description</th>
            <th>Created By</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="game in games" :key="game.id">
            <td>{{ game.id }}</td>
            <td>{{ game.title }}</td>
            <td>{{ game.slug }}</td>
            <td>{{ game.description }}</td>
            <td>{{ game.author }}</td>
            <td>
              <button @click="editGame(game.id)">Edit</button>
              <button @click="deleteGame(game.id)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p class="errors" style="color: red;">{{ error }}</p>
    </div>
  </div>
</template>
