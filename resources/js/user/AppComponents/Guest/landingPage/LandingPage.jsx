import Axios from "axios";
import React, { useEffect, useState } from "react";
import HeaderNav from "../../Navigation/HeaderNav";
import FoodMenu from "../meal/index";
import FailModal from "./FailModal";
import SuccessModal from "./SuccessModal";
import WelcomeModal from "./WelcomeModal";
import { useSelector, useDispatch } from "react-redux";
import Preloader from "../../ReuseableCompononts/Preloader";
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

export default function LandingPage(props) {
  const dispatch = useDispatch();

  const done = useSelector((state) => state.done);

  const [states, setStates] = useState([]);
  const [open, setOpen] = useState(true);
  const [openFail, setOpenFail] = useState(false);
  const [openSuccess, setOpenSuccess] = useState(false);
  const [success, setSuccess] = useState(false);
  const [persons, setPersons] = useState([""]);
  const [person, setPerson] = useState("");
  const [numberOfPersons, setNumberOfPersons] = useState([]);
  const [step, setStep] = useState(1);
  const [location, setLocation] = useState(false);
  const [meals, setMeals] = useState([]);
  const [token, setToken] = useState("");
  const [initialCart, setInitialCart] = useState([]);
  const [finish, setFinish] = useState(false);
  const [cartSize, setCartSize] = useState(0);

  const notifySuccess = (text) => toast.success(text);
  const notifyWarning = (text) => toast.warning(text);

  const changePerson = () => {
    setPerson(persons[step]);
    setStep(step + 1);
  };
  const changePerson1 = () => {
    setPerson(numberOfPersons[step]);
    setStep(step + 1);
  };

  const handleAddCart = async (data) => {
    let storedCart = localStorage.getItem("cart");
    let parsedStoredCart = JSON.stringify(storedCart);
    if (token === undefined || token === null) {
      localStorage.setItem(
        "cart",
        JSON.stringify([...parsedStoredCart, { ...data, name: person }])
      );
    } else {
      Axios.post(
        "/api/cart/new",
        {
          ...data,
          name: person,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
        }
      )
        .then((res) => {
          localStorage.setItem(
            "cart",
            JSON.stringify([...initialCart, { ...data, name: person }])
          );
          setInitialCart([...initialCart, { ...data, name: person }]);
        })
        .catch((e) => {});
    }
    notifySuccess(`Meal added to cart`);
    setCartSize(cartSize + 1);
  };

  useEffect(() => {
    let location = localStorage.getItem("location");
    let parsedLocation = JSON.parse(location);
    let token = localStorage.getItem("token");
    let cart = localStorage.getItem("cart");
    let parsedCart = JSON.parse(cart);
    setFinish(done.done);
    setToken(token);
    if (parsedCart === undefined || parsedCart === null) {
      setInitialCart([]);
    } else {
      setInitialCart(parsedCart);
      setCartSize(parsedCart.length);
    }
    if (parsedLocation) {
      setLocation(true);
      setOpenSuccess(true);
      setOpen(false);
    }
    Axios.get("/api/state/list").then((res) => {
      setStates(res.data.states);
    });

    Axios.get("/api/meal/list").then((res) => {
      setMeals(res.data.data);
    });
  }, []);

  useEffect(() => {
    if (success) {
      let names = localStorage.getItem("names");
      let parsedNames = JSON.parse(names);
      setPersons(parsedNames);
      setPerson(parsedNames[0]);
      if (parsedNames.length === 1) {
        for (var i = 1; i <= new Array(parsedNames[0]).length; i++) {
          numberOfPersons.push(i);
        }
        setPerson(numberOfPersons[0]);
      }
    }
  }, [success]);

  return (
    <div>
      <HeaderNav
        cartSize={cartSize}
        home={true}
        setSuccess={setSuccess}
        setOpen={setOpen}
      />
      <ToastContainer
        position="top-right"
        autoClose={3000}
        hideProgressBar={false}
        newestOnTop={false}
        closeOnClick
        rtl={false}
        pauseOnFocusLoss
        draggable
        pauseOnHover
      />
      {!finish && (
        <div>
          {states.length > 0 && !openFail && !openSuccess && open && (
            <WelcomeModal
              open={open}
              openFail={openFail}
              setOpenFail={setOpenFail}
              openSuccess={openSuccess}
              setOpenSuccess={setOpenSuccess}
              setOpen={setOpen}
              states={states}
              {...props}
            />
          )}
          {openFail && !openSuccess && (
            <FailModal
              notifyWarning={notifyWarning}
              open={open}
              openFail={openFail}
              setOpenFail={setOpenFail}
              openSuccess={openSuccess}
              setOpenSuccess={setOpenSuccess}
              setOpen={setOpen}
            />
          )}
          {!openFail && openSuccess && (
            <SuccessModal
              notifySuccess={notifySuccess}
              location={location}
              setSuccess={setSuccess}
              open={open}
              openFail={openFail}
              setOpenFail={setOpenFail}
              openSuccess={openSuccess}
              setOpenSuccess={setOpenSuccess}
              setOpen={setOpen}
            />
          )}
        </div>
      )}
      <div>
        {/* <!--============ THE FOOD LISTING ==========--> */}
        <div className="container">
          <h5 className="mt-3" style={{ color: "black" }}>
            {success
              ? persons.length > 1
                ? `For ${person}`
                : `For Person ${step}`
              : ""}
          </h5>
          <div className="row">
            {/* <!-- ===first col==== --> */}
            <div className="col-lg-8 col-md-8 col-sm-12 col-xs-12">
              {meals.length > 0 ? (
                <FoodMenu
                  notifySuccess={notifySuccess}
                  handleAddCart={handleAddCart}
                  person={person}
                  meals={meals}
                />
              ) : (
                <Preloader />
              )}
              <div className="mt-3 pb-5">
                {persons.length > 1 && step !== persons.length && (
                  <button
                    onClick={changePerson}
                    className="btn btn-style btn-lg"
                  >
                    NEXT PERSON
                  </button>
                )}
                {persons.length === 1 && step < numberOfPersons.length && (
                  <button
                    onClick={changePerson1}
                    className="btn btn-style btn-lg"
                  >
                    NEXT PERSON
                  </button>
                )}
              </div>
            </div>

            {/* <!-- ===second col==== --> */}
            <div className="col-lg-4 col-md-4 col-sm-12 col-xs-12">
              <div className="card text-center pay-box shadow-lg">
                <div className="card-body">
                  <h5 className="card-title card-head ">Tips</h5>
                  <p className="card-text text-left">
                    All transactions must be completed before delivery.
                  </p>
                  <p className="card-text text-left">
                    All transactions must be completed before delivery.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
