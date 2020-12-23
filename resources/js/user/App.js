import "bootstrap/dist/css/bootstrap.min.css";
import React from "react";
import { Provider } from "react-redux";
import { BrowserRouter as Router, Route, Switch } from "react-router-dom";
import "./App.css";
import SignIn from "./AppComponents/Auth/Signin";
import SignUp from "./AppComponents/Auth/Signup";
import LandingPage from "./AppComponents/Guest/landingPage/LandingPage";
import Cart from "./AppComponents/User/cart/Cart";
import Checkout from "./AppComponents/User/Checkout";
import ClosedOder from "./AppComponents/User/ClosedOder";
import Dashboard from "./AppComponents/User/Dashboard";
import EditProfile from "./AppComponents/User/EditProfile";
import OpenOder from "./AppComponents/User/OpenOder";
import Password from "./AppComponents/User/Password";
import store from "./Redux/store";
import WithAuth from "./WithAuth";


function App(props) {
  return (
    <Provider store={store}>
      <Router>
        <div className="App">
          <Switch>
            <Route
              exact
              path="/"
              render={(props) => {
                return <LandingPage {...props} />;
              }}
            />
            <Route
              {...props}
              exact
              path="/checkout"
              component={WithAuth(Checkout)}
            />
            <Route
              {...props}
              exact
              path="/account"
              component={WithAuth(Dashboard)}
            />
            <Route
              {...props}
              exact
              path="/account/edit"
              component={WithAuth(EditProfile)}
            />
            <Route
              {...props}
              exact
              path="/account/open"
              component={WithAuth(OpenOder)}
            />
            <Route
              {...props}
              exact
              path="/account/close"
              component={WithAuth(ClosedOder)}
            />
            <Route
              {...props}
              exact
              path="/account/password"
              component={WithAuth(Password)}
            />

            <Route
              exact
              path="/cart"
              render={(props) => {
                return <Cart {...props} />;
              }}
            />
            <Route
              exact
              path="/signin"
              render={(props) => {
                return <SignIn {...props} />;
              }}
            />
            <Route
              exact
              path="/signup"
              render={(props) => {
                return <SignUp {...props} />;
              }}
            />
          </Switch>
        </div>
      </Router>
    </Provider>
  );
}

export default App;
