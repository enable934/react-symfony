import React, {Component} from 'react';
import axios from 'axios';

class Restaurant extends Component {
    constructor() {
        super();
        this.state = {
            restaurants: [],
            loading: true,
        };
    }

    componentDidMount() {
        this.getRestaurants();
    }

    getRestaurants() {
        axios.get(`http://localhost:25558/api/restaurants`).then(restaurants => {
            this.setState({restaurants: restaurants.data, loading: false})
        })
    }

    render() {
        const loading = this.state.loading;
        return (
            <div>
                <section className="row-section">
                    <div className="container">
                        <div className="row">
                            <h2 className="text-center"><span>Усі Ресторани</span><i className="fa fa-heart"/></h2>
                        </div>
                        {loading ? (
                            <div className={'row text-center'}>
                                <span className="fa fa-spin fa-spinner fa-4x"/>
                            </div>
                        ) : (
                            <div className={'row'}>
                                {this.state.restaurants.map(restaurant =>
                                    <div className="col-md-10 offset-md-1 row-block" key={restaurant.id}>
                                        <div>
                                            <div>
                                                <h5>{restaurant.name}</h5>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                </section>
            </div>
        )
    }
}

export default Restaurant;