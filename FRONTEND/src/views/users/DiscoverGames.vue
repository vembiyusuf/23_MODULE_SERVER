<template>
  <div class="container">
     <NavbarUser/>
    <h1 class="title">Discover Games</h1>

    <div class="sort-controls">
      <label>
        Sort by:
        <select v-model="sort.by" @change="resetAndFetch">
          <option value="uploadTimestamp">Recently Updated</option>
          <option value="scoreCount">Popularity</option>
          <option value="title">Title</option>
        </select>
      </label>
      <label>
        Order:
        <select v-model="sort.order" @change="resetAndFetch">
          <option value="desc">Descending</option>
          <option value="asc">Ascending</option>
        </select>
      </label>
    </div>

    <div class="game-list">
      <div
        v-for="game in games"
        :key="game.slug"
        class="game-card"
      >
        <img
          :src="game.thumbnail || defaultThumbnail"
          :alt="game.title"
          class="thumbnail"
        />
        <div class="info">
          <router-link
            :to="`/games/${game.slug}`"
            class="game-title"
            aria-label="View game details"
          >
            {{ game.title }}
          </router-link>
          <p class="description">{{ game.description }}</p>
          <p class="score-count">{{ game.scoreCount }} score(s)</p>
        </div>
      </div>
    </div>

    <p v-if="loading" class="loading">Loading more games...</p>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import NavbarUser from '@/components/navbarUser.vue'

const games = ref([])
const page = ref(0)
const size = 3
const loading = ref(false)
const finished = ref(false)

const defaultThumbnail = 'https://via.placeholder.com/150?text=No+Image'

const sort = ref({
  by: 'uploadTimestamp',
  order: 'desc',
})

async function fetchGames() {
  if (loading.value || finished.value) return
  loading.value = true
  try {
    const res = await axios.get('/games', {
      params: {
        page: page.value,
        size,
        sort: `${sort.value.by},${sort.value.order}`,
      },
    })

    const newGames = res.data.content || []
    if (newGames.length === 0) {
      finished.value = true
    } else {
      games.value.push(...newGames)
      page.value++
    }
  } catch (err) {
    console.error('Failed to fetch games:', err)
  }
  loading.value = false
}

function resetAndFetch() {
  games.value = []
  page.value = 0
  finished.value = false
  fetchGames()
}

function handleScroll() {
  const scrollBottom =
    window.innerHeight + window.scrollY >= document.body.offsetHeight - 100
  if (scrollBottom) fetchGames()
}

onMounted(() => {
  fetchGames()
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
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

.sort-controls {
  margin-bottom: 20px;
  display: flex;
  gap: 20px;
}

.sort-controls label {
  font-size: 14px;
}

.game-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.game-card {
  display: flex;
  gap: 16px;
  border: 1px solid #ccc;
  border-radius: 10px;
  padding: 12px;
  background-color: #fdfdfd;
}

.thumbnail {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 6px;
}

.info {
  flex: 1;
}

.game-title {
  font-size: 18px;
  font-weight: bold;
  color: #0066cc;
  text-decoration: none;
  display: inline-block;
  margin-bottom: 6px;
}

.game-title:focus,
.game-title:hover {
  text-decoration: underline;
}

.description {
  font-size: 14px;
  margin-bottom: 6px;
}

.score-count {
  font-size: 13px;
  color: #666;
}

.loading {
  text-align: center;
  margin-top: 20px;
  font-style: italic;
}
</style>
