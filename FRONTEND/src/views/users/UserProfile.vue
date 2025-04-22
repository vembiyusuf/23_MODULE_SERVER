<template>
  <div class="container">
    <navbarUser/>
    <h1 class="username">User: {{ profile.username }}</h1>

    <div v-if="profile.authoredGames.length > 0" class="section">
      <h2 class="section-title">Authored Games</h2>
      <div class="game-list">
        <div v-for="game in authoredGamesSorted" :key="game.slug" class="game-card">
          <img
            v-if="game.thumbnail"
            :src="game.thumbnail"
            alt="Thumbnail"
            class="thumbnail"
          />
          <h3 class="game-title">{{ game.title }}</h3>
          <p class="game-description">{{ game.description }}</p>
          <p class="game-score-count">{{ game.score_count }} submitted scores</p>
        </div>
      </div>
    </div>

    <!-- Highscores -->
    <div class="section">
      <h2 class="section-title">Highscores</h2>
      <div v-if="profile.highscores.length > 0" class="highscore-list">
        <div v-for="score in highscoresSorted" :key="score.game.slug" class="score-card">
          <h3 class="game-title">
            <router-link :to="`/games/${score.game.slug}`">{{ score.game.title }}</router-link>
          </h3>
          <p class="game-description">{{ score.game.description }}</p>
          <p class="game-score">Highscore: {{ score.score }}</p>
          <p class="timestamp">Timestamp: {{ formatDate(score.timestamp) }}</p>
        </div>
      </div>
      <p v-else class="no-data">No highscores found.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import navbarUser from '@/components/navbarUser.vue'

const route = useRoute()
const username = route.params.username

const profile = ref({
  username: '',
  authoredGames: [],
  highscores: []
})

const authoredGamesSorted = ref([])
const highscoresSorted = ref([])

onMounted(async () => {
  try {
    const res = await axios.get(`/users/${username}`)
    profile.value = res.data

    // Ensure authoredGames is always an array
    profile.value.authoredGames = Array.isArray(profile.value.authoredGames)
      ? profile.value.authoredGames
      : []

    // Ensure highscores is always an array
    profile.value.highscores = Array.isArray(profile.value.highscores)
      ? profile.value.highscores
      : []

    // Now you can safely map and sort
    profile.value.authoredGames = profile.value.authoredGames.map(game => ({
      ...game,
      thumbnail: `/storage/thumbnails/${game.slug}.jpg`,
      score_count: Math.floor(Math.random() * 100)
    }))

    authoredGamesSorted.value = profile.value.authoredGames.sort((a, b) =>
      b.slug.length - a.slug.length
    )

    highscoresSorted.value = profile.value.highscores.sort((a, b) =>
      a.game.title.localeCompare(b.game.title)
    )
  } catch (err) {
    console.error(err)
    alert('User not found or failed to load profile.')
  }
})


function formatDate(dateStr) {
  const d = new Date(dateStr)
  return d.toLocaleString()
}
</script>

<style scoped>
.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
  font-family: sans-serif;
}

.username {
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 20px;
}

.section {
  margin-bottom: 40px;
}

.section-title {
  font-size: 22px;
  margin-bottom: 16px;
}

.game-list, .highscore-list {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.game-card, .score-card {
  border: 1px solid #ccc;
  border-radius: 10px;
  padding: 16px;
  width: calc(50% - 10px);
  box-sizing: border-box;
  background-color: #f9f9f9;
}

.thumbnail {
  width: 100%;
  height: 180px;
  object-fit: cover;
  margin-bottom: 10px;
  border-radius: 6px;
}

.game-title {
  font-size: 18px;
  font-weight: bold;
  margin: 5px 0;
}

.game-description {
  font-size: 14px;
  color: #555;
  margin-bottom: 6px;
}

.game-score-count, .game-score {
  font-size: 14px;
  color: #0077cc;
  margin-bottom: 4px;
}

.timestamp {
  font-size: 12px;
  color: #888;
}

.no-data {
  font-size: 14px;
  color: #888;
}
</style>
