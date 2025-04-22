<template>
  <div class="container">
    <NavbarUser />
    <h1 class="title">Manage Game</h1>

    <div class="form-input">
      <input type="text" v-model="title" placeholder="Title Game" />
      <input type="text" v-model="description" placeholder="Description Game" />
      <input type="file" @change="handleFileUpload" />
      <button @click="addGame">Add Game</button>
    </div>

    <p class="success" v-if="successMessage">{{ successMessage }}</p>
    <p class="error" v-if="errorMessage">{{ errorMessage }}</p>

    <div class="game-table">
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Slug</th>
            <th>Description</th>
            <th>Created By</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="game in games" :key="game.slug">
            <td>{{ game.title }}</td>
            <td>{{ game.slug }}</td>
            <td>{{ game.description }}</td>
            <td>{{ game.author }}</td>
            <td>
              <button @click="deleteGame(game.slug)">Hapus</button>
              <button @click="uploadNewVersion(game.slug)">Upload Version</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import NavbarUser from '@/components/navbarUser.vue'
import { useRouter } from 'vue-router'

const title = ref('')
const description = ref('')
const file = ref(null)
const successMessage = ref('')
const errorMessage = ref('')
const games = ref([])
const router = useRouter()

const addGame = async () => {
  try {
    const token = localStorage.getItem('token')
    await axios.post('/games', { title: title.value, description: description.value }, {
      headers: { Authorization: `Bearer ${token}` }
    })

    successMessage.value = 'Game berhasil ditambahkan!'
    errorMessage.value = ''
    title.value = ''
    description.value = ''
    await fetchGames()
  } catch (err) {
    successMessage.value = ''
    errorMessage.value = 'Gagal menambahkan game.'
  }
}

const fetchGames = async () => {
  try {
    const res = await axios.get('/games')
    games.value = res.data.content
  } catch (err) {
    errorMessage.value = 'Gagal mengambil data.'
  }
}

const deleteGame = async (slug) => {
  if (!confirm('Yakin ingin menghapus game ini?')) return
  try {
    await axios.delete(`/games/${slug}`)
    successMessage.value = 'Game berhasil dihapus!'
    await fetchGames()
  } catch (err) {
    errorMessage.value = 'Gagal menghapus game.'
  }
}

const uploadNewVersion = async (slug) => {
  const formData = new FormData()
  if (!file.value) {
    alert('Please select a file to upload.')
    return
  }
  
  formData.append('file', file.value)
  
  try {
    const token = localStorage.getItem('token')
    const res = await axios.post(`/games/${slug}/upload`, formData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data',
      }
    })

    successMessage.value = 'New version uploaded successfully!'
    await fetchGames()
  } catch (err) {
    errorMessage.value = 'Failed to upload new version.'
  }
}

const handleFileUpload = (event) => {
  file.value = event.target.files[0]
}

onMounted(() => {
  fetchGames()
  checkUserPermission()
})

const checkUserPermission = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get('/user', { headers: { Authorization: `Bearer ${token}` } })
    if (!res.data.is_author) {
      router.push(`/games/${res.data.game_slug}`) 
    }
  } catch (err) {
    console.error('Permission check failed:', err)
  }
}
</script>

<style scoped>
.container {
  max-width: 900px;
  margin: auto;
  padding: 20px;
  font-family: sans-serif;
}

.title {
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 20px;
}

.form-input {
  display: flex;
  gap: 10px;
  margin-bottom: 16px;
}

.form-input input {
  padding: 8px;
  font-size: 14px;
  width: 200px;
}

button {
  padding: 8px 14px;
  font-size: 14px;
  cursor: pointer;
}

.success {
  color: green;
  margin-bottom: 10px;
}

.error {
  color: red;
  margin-bottom: 10px;
}

.game-table {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

th,
td {
  padding: 10px;
  border: 1px solid #ccc;
  text-align: left;
}

th {
  background-color: #f5f5f5;
}
</style>
