import React, {Component} from 'react';
import {Route, Switch, Redirect, Link} from 'react-router-dom';
import Restaurant from "./Restaurant";
import Tables from "./Tables";

class Home extends Component {

    render() {
        return (
                <div>
                    <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
                        <Link className={"navbar-brand"} to={"/"}> Restaurant </Link>
                        <div className="collapse navbar-collapse" id="navbarText">
                            <ul className="navbar-nav mr-auto">
                                <li className="nav-item">
                                    <Link className={"nav-link"} to={"/restaurants"}> Ресторани </Link>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <Switch>
                        <Redirect exact from="/" to="/restaurants"/>
                        <Route path="/restaurants" component={Restaurant}/>
                        <Route path="/restaurant/:id" component={Tables}/>
                    </Switch>
                </div>
        )
    }
}

export default Home;