import axios from '@/utils/axios'

class AuthService {
  async login(email, password) {
    const response = await axios.post('/login', {
      email,
      password
    })
    if (response.data.token) {
      localStorage.setItem('user', JSON.stringify(response.data))
    }
    return response.data
  }

  async register(name, email, password, password_confirmation) {
    const response = await axios.post('/register', {
      name,
      email,
      password,
      password_confirmation
    })
    if (response.data.token) {
      localStorage.setItem('user', JSON.stringify(response.data))
    }
    return response.data
  }

  logout() {
    localStorage.removeItem('user')
  }

  async getCurrentUser() {
    try {
      const response = await axios.get('/me')
      return response.data
    } catch (error) {
      if (error.response?.status === 401) {
        this.logout()
      }
      throw error
    }
  }

  async refreshToken() {
    try {
      const response = await axios.post('/refresh')
      if (response.data.token) {
        const user = JSON.parse(localStorage.getItem('user') || '{}')
        user.token = response.data.token
        localStorage.setItem('user', JSON.stringify(user))
      }
      return response.data
    } catch (error) {
      this.logout()
      throw error
    }
  }
}

export default new AuthService() 