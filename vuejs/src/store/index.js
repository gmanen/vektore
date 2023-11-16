import { createStore } from 'vuex';
import axios from '@axios'
import config from "@/config";
import router from '../router'
import jwtDecode from "jwt-decode";

export default createStore({
    state: {
        authenticated: false,
        user: null,
        token: null,
        tokenExpiration: null,
        lastRefresh: null,
        companies: [],
        currentCompany: null,
        users: [],
        roles: []
    },
    mutations: {
        initStore: state => {
            state.token = localStorage.getItem('token')
            state.tokenExpiration = localStorage.getItem('token_expiration')
            state.lastRefresh = localStorage.getItem('last_refresh')
            state.user = JSON.parse(localStorage.getItem('user'))
            state.companies = JSON.parse(localStorage.getItem('companies'))
            state.currentCompany = JSON.parse(localStorage.getItem('current_company'))
            state.roles = JSON.parse(localStorage.getItem('roles'))
        },
        setAuthenticated: (state, authenticated) => {
            state.authenticated = authenticated
        },
        setToken: (state, token) => {
            localStorage.setItem('token', token)
            state.token = token
        },
        setTokenExpiration: (state, token) => {
            const tokenExpiration = token ? Date.now() + (3300 * 1000) : 0

            localStorage.setItem('token_expiration', tokenExpiration)
            state.tokenExpiration = tokenExpiration
        },
        setLastRefresh: (state, lastRefresh) => {
            localStorage.setItem('last_refresh', lastRefresh)
            state.lastRefresh = lastRefresh
        },
        setUser: (state, user) => {
            localStorage.setItem('user', user ? JSON.stringify(user) : null)
            state.user = user
        },
        setCompanies: (state, companies) => {
            localStorage.setItem('companies', companies ? JSON.stringify(companies) : [])
            state.companies = companies
        },
        setCurrentCompany: (state, company) => {
            localStorage.setItem('current_company', company ? JSON.stringify(company) : null)
            state.currentCompany = company
        },
        setRoles: (state, roles) => {
            localStorage.setItem('roles', roles ? JSON.stringify(roles) : [])
            state.roles = roles
        }
    },
    actions: {
        loginCas: ({ dispatch, state, commit }, data) => {
            commit('setToken', data.data.token)
            commit('setTokenExpiration', data.data.token)
            commit('setAuthenticated', true)
            commit('setLastRefresh', Date.now())

            dispatch('getUser').then(() => {})
        },
        logout: (state, type) => {
            const isCasUser = state.getters.isCasUser

            state.commit('setToken', null)
            state.commit('setTokenExpiration', null)
            state.commit('setUser', null)
            state.commit('setAuthenticated', false)
            state.commit('setCompanies', [])
            state.commit('setCurrentCompany', null)
            state.commit('setRoles', [])
            state.commit('setLastRefresh', null)

            if (isCasUser) {
                router.push({ name: 'LoginCas' })
            } else {
                router.push({ name: 'Login' })
            }
        },
        refreshToken: (state) => {
            axios
                .get(config.apiUrl + '/refresh-token', {
                    headers: {
                        Authorization: 'Bearer ' + state.getters.getToken
                    }
                })
                .then(response => {
                    if (response.status === 200) {
                        const data = response.data

                        if (data) {
                            state.commit('setToken', data.token)
                            state.commit('setTokenExpiration', data.token)
                            state.commit('setLastRefresh', Date.now())
                        }
                    }
                })
                .catch(e => {
                    const response = e.response

                    if (response && response.status === 401) {
                        state.dispatch('logout', 'timeout')
                    }
                })
        },
        getUser: (state) => {
            const token = state.getters.getToken
            const data = jwtDecode(token)

            return axios
                .get(config.apiUrl + '/users/' + data.userId, {
                    headers: {
                        Authorization: 'Bearer ' + state.getters.getToken
                    }
                })
                .then(response => {
                    if (response.status === 200) {
                        const user = response.data

                        state.commit('setUser', user)

                        router.push({ name: 'DashboardCas' })
                    }
                })
                .catch(() => {
                })
        },
    },
    getters: {
        isAuthenticated: (state, getters) => {
            return !getters.isTokenExpired
        },
        getToken: state => {
            return state.token
        },
        isTokenExpired: state => {
            return Date.now() > state.tokenExpiration
        },
        getUser: state => {
            return state.user
        },
        getRoles: state => {
            return state.roles
        },
        getCompanies: state => {
            return state.companies
        },
        getCurrentCompany: state => {
            return state.currentCompany
        },
        getLastRefresh: state => {
            return state.lastRefresh
        },
        isCasUser: (state, getters) => {
            return (getters.isAuthenticated && state.user.cas === true) || window.location.hostname.includes('fiducial.dom')
        }
    }
});
