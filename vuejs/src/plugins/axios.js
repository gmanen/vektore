import axios from 'axios'
import router from '@/router'

const axiosIns = axios.create({
// You can add your headers here
// ================================
// baseURL: 'https://some-domain.com/api/',
// timeout: 1000,
// headers: {'X-Custom-Header': 'foobar'}
})

export default axiosIns