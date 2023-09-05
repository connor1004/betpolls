import React, { Component } from 'react';
import {
  Card, Form, InlineError, Button,
  TextField, ResourceList, Stack, TextStyle
} from '@shopify/polaris';
import { Formik } from 'formik';
import * as Yup from 'yup';

import Api from '../../../apis/app';
import {
  BET_TYPE
} from '../../../configs/enums';

const BetTypeHeader = () => (
  <Card>
    <ResourceList.Item>
      <Stack>
        <Stack.Item>
          <div style={{ width: '120px' }}>
            <TextStyle variation="strong">Bet type</TextStyle>
          </div>
        </Stack.Item>
        <Stack.Item fill>
          <div style={{ width: '150px' }}>
            <TextStyle variation="strong">Win score</TextStyle>
          </div>
        </Stack.Item>
        <Stack.Item fill>
          <div style={{ width: '150px' }}>
            <TextStyle variation="strong">Loss score</TextStyle>
          </div>
        </Stack.Item>
        <Stack.Item fill>
          <div style={{ width: '150px' }}>
            <TextStyle variation="strong">Tie win score</TextStyle>
          </div>
        </Stack.Item>
        <Stack.Item fill>
          <div style={{ width: '150px' }}>
            <TextStyle variation="strong">Tie loss score</TextStyle>
          </div>
        </Stack.Item>
      </Stack>
    </ResourceList.Item>
  </Card>
);

const BetTypeItem = (props) => {
  const {
    onChange, data
  } = props;
  return (
    <div>
      <Formik
        initialValues={{
          win_score: data.win_score,
          loss_score: data.loss_score,
          tie_win_score: data.tie_win_score,
          tie_loss_score: data.tie_loss_score
        }}
        validationSchema={
          Yup.object().shape({
            win_score: Yup.number().required('Win score is required!'),
            loss_score: Yup.number().required('Loss score is required!'),
            tie_win_score: Yup.number(),
            tie_loss_score: Yup.number()
          })
        }
        onSubmit={onChange}
        render={({
          values,
          errors,
          touched,
          handleBlur,
          setFieldValue,
          handleSubmit
        }) => (
          <Form onSubmit={handleSubmit}>
            <Card>
              <ResourceList.Item>
                <Stack>
                  <Stack.Item>
                    <div style={{ width: '120px' }}>
                      <TextStyle variation="strong">{data.name}</TextStyle>
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <div style={{ width: '150px' }}>
                      <TextField
                        placeholder="Win score"
                        name="win_score"
                        value={values.win_score}
                        onBlur={handleBlur}
                        onChange={async (value) => {
                          await setFieldValue('win_score', value);
                          await handleSubmit();
                        }}
                        error={touched.win_score && errors.win_score}
                      />
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <div style={{ width: '150px' }}>
                      <TextField
                        placeholder="Loss score"
                        name="loss_score"
                        value={values.loss_score}
                        onBlur={handleBlur}
                        onChange={async (value) => {
                          await setFieldValue('loss_score', value);
                          await handleSubmit();
                        }}
                        error={touched.loss_score && errors.loss_score}
                      />
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <div style={{ width: '150px' }}>
                      {(data.value === BET_TYPE.MONEYLINE) && (
                        <TextField
                          placeholder="Tie win score"
                          name="tie_win_score"
                          value={values.tie_win_score}
                          onBlur={handleBlur}
                          onChange={async (value) => {
                            await setFieldValue('tie_win_score', value);
                            await handleSubmit();
                          }}
                          error={touched.tie_win_score && errors.tie_win_score}
                        />
                      )}
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <div style={{ width: '150px' }}>
                      {(data.value === BET_TYPE.MONEYLINE) && (
                        <TextField
                          placeholder="Tie loss score"
                          name="tie_loss_score"
                          value={values.tie_loss_score}
                          onBlur={handleBlur}
                          onChange={async (value) => {
                            await setFieldValue('tie_loss_score', value);
                            await handleSubmit();
                          }}
                          error={touched.tie_loss_score && errors.tie_loss_score}
                        />
                      )}
                    </div>
                  </Stack.Item>
                </Stack>
              </ResourceList.Item>
            </Card>
          </Form>
        )}
      />
    </div>
  );
};

class BetSettings extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isSubmitting: false,
      error: false,
      bet_types: []
    };
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  async componentDidMount() {
    const {
      response, body
    } = await Api.get('admin/generals/bet-types');
    switch (response.status) {
      case 200:
        this.setState({
          bet_types: body
        });
        break;
      default:
        break;
    }
  }

  async handleSubmit() {
    await this.setState({
      isSubmitting: true
    });
    const {
      bet_types
    } = this.state;
    const {
      body, response
    } = await Api.put('admin/generals/bet-types', { bet_types });

    switch (response.status) {
      case 200:
        this.setState({
          bet_types: body,
          error: false
        });
        break;
      default:
        this.setState({
          error: 'Error has been occured'
        });
        break;
    }

    await this.setState({
      isSubmitting: false
    });
  }

  handleChange(index, values) {
    const {
      bet_types
    } = this.state;
    const bet_type = bet_types[index];
    bet_type.win_score = values.win_score;
    bet_type.loss_score = values.loss_score;
    bet_type.tie_win_score = values.tie_win_score;
    bet_type.tie_loss_score = values.tie_loss_score;
    this.setState({
      bet_types
    });
  }

  render() {
    const {
      bet_types, isSubmitting, error
    } = this.state;

    return (
      <Card title="Bet Settings" sectioned>
        <InlineError message={error} />
        <BetTypeHeader />
        {
          bet_types.map((bet_type, index) => (
            <BetTypeItem key={`${index}`} data={bet_type} onChange={this.handleChange.bind(this, index)} />
          ))
        }
        <Button
          onClick={this.handleSubmit}
          loading={isSubmitting}
          submit
          primary
          >
            Update
        </Button>
      </Card>
    );
  }
}

export default BetSettings;
