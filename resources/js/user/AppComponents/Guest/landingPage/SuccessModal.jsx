import React, { useEffect, useState } from "react";
import Modal from "react-modal";
import { useDispatch } from "react-redux";
import { DONE } from "../../../Redux/types";

const customStyles = {
  content: {
    top: "50%",
    left: "50%",
    right: "auto",
    bottom: "auto",
    marginRight: "-50%",
    transform: "translate(-50%, -50%)",
    backgroundColor: "#ff7417",
  },
};

const SuccessModal = (props) => {
  const dispatch = useDispatch();
  const [step, setStep] = useState(1);
  const [number, setNumber] = useState(1);
  const [names, setNames] = useState({});
  const [numberOfPersons, setNumberOfPersons] = useState([]);

  const continue1 = () => {
    setStep(3);
    if (number > 1) {
      for (var i = 1; i <= new Array(Number(number)).length; i++) {
        numberOfPersons.push(i);
      }
    } else {
      localStorage.removeItem("names");
      localStorage.setItem("names", JSON.stringify([1]));
      props.setSuccess(true);
      continue2();
      props.notifySuccess("You're shopping for 1 person");
    }
  };

  const continue2 = () => {
    props.setOpenSuccess(false);
    props.setOpenFail(false);
    props.setOpen(false);
    dispatch({ type: DONE });
  };

  const onChange = (e, index) => {
    setNames({
      ...names,
      [index]: e.target.value,
    });
  };

  const done1 = () => {
    localStorage.setItem("names", JSON.stringify([Number(number)]));
    props.setSuccess(true);
    props.notifySuccess(`You're shopping for ${number} persons`);
    continue2();
  };

  const done = () => {
    localStorage.setItem("names", JSON.stringify(Object.values(names)));
    props.setSuccess(true);
    props.notifySuccess(`You're shopping for ${number} persons`);
    continue2();
  };

  useEffect(() => {
    if (props.location) {
      setStep(2);
    }
  }, []);

  const handleNumber = (e) => {
    setNumber(e.target.value);
  };
  return (
    <Modal
      isOpen={props.openSuccess}
      onRequestClose={() => null}
      style={customStyles}
      contentLabel="Success"
    >
      {step === 1 && (
        <div>
          <div className="py-5 white">
            Congrats! We can deliver to your location, please click below to
            continue……
          </div>
          <div>
            <button onClick={() => setStep(2)} className="btn btn-sm modal-btn">
              Continue
            </button>
          </div>
        </div>
      )}
      {step === 2 && (
        <div>
          <div className="py-3 white">
            How many persons are you ordering for?
          </div>
          <div>
            <input
              value={number}
              name="number"
              onChange={handleNumber}
              type="number"
              className="form-control"
            />
            <button onClick={continue1} className="btn btn-sm modal-btn mt-4">
              Continue
            </button>
          </div>
        </div>
      )}
      {step === 3 && number > 1 && (
        <div>
          <div className="py-3 white">
            Would you like to personalize your order?
          </div>
          <div>
            <button onClick={done1} className="btn btn-primary mt-4">
              No
            </button>
            <button
              onClick={() => setStep(4)}
              className="btn modal-btn mt-4 ml-2"
            >
              Yes
            </button>
          </div>
        </div>
      )}
      {step === 4 && (
        <div
          style={{ height: "70vh", overflowY: "scroll" }}
          className="pl-2 pr-4"
        >
          <h4 className="white">
            Please enter the name of the {number} persons
          </h4>
          {numberOfPersons.map((data, index) => (
            <div className="mt-2">
              <label className="white">Person {data}</label>
              <input
                name="text"
                onChange={(e) => onChange(e, index)}
                type="text"
                className="form-control"
                placeholder="Mark Essien"
              />
            </div>
          ))}
          <button onClick={() => done()} className="btn btn-sm modal-btn mt-4">
            Continue
          </button>
        </div>
      )}
    </Modal>
  );
};

export default SuccessModal;
