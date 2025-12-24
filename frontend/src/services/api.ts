import router from "@/router";
import axios from "axios";

const api = axios.create({
	baseURL: '/api',
	headers: {
		'Accept': 'application/json',
		'X-Requested-With': 'XMLHttpRequest'
	},
	withCredentials: true,
	withXSRFToken: true,
	timeout: 5000
})

api.interceptors.response.use(
	// Return the response if the request was successful
	(response) => response,
	
	async (error) => {
		const originalRequest = error.config

		// Safely check the status code using Optional Chaining
		const status = error.response?.status

		if(error.response.status === 419 && !originalRequest._retry ){
			originalRequest._retry = true;
				
		/**
         * Handle 419 (CSRF Token Mismatch/Expired)
         * Only retry once using the custom '_retry' flag
         */
			try{
				// Refresh the CSRF cookie from Laravel Sanctum
				await api.get('token');

				// Retry the original request with the new cookie
				return api(originalRequest);
			}catch(retryError){
				// If fetching the token itself fails, stop and reject
				return Promise.reject(retryError)
			}

			
		}

		// Redirect to login page if the session has expired
		if( status === 401){
			router.push({name:'login'})
		}

		// Always reject the promise so the Error can be caught in Vue component
		return Promise.reject(error);
	}
)

export default api