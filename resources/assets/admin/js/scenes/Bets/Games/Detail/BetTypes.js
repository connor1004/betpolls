import React, { Component, Fragment } from 'react';
import {
  Card, Form, Button,
  TextField,
  Stack,
  ResourceList,
  TextStyle
} from '@shopify/polaris';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  BET_TYPE
} from '../../../../configs/enums';

class BetTypes extends Component {
  constructor(props) {
    super(props);
    this.formikRef = React.createRef();
    this.handleCreate = this.handleCreate.bind(this);
    this.handleUpdateItem = this.handleUpdateItem.bind(this);
  }

  async handleUpdateItem(values, bags) {
    const {
      onUpdateItem
    } = this.props;
    await onUpdateItem(values, bags, this);
  }

  async handleCreate() {
    const {
      onCreate
    } = this.props;
    await onCreate();
  }

  renderHeader() {
    const {
      game
    } = this.props;
    const homeTeamFirst = game && (game.between_players || (game.league && game.league.sport_category && game.league.sport_category.home_team_first === 1));
    return (
      <Card>
        <ResourceList.Item>
          <Stack>
            <Stack.Item>
              <div style={{ width: '60px' }}>
                <TextStyle variation="strong">ID</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item>
              <div style={{ width: '120px' }}>
                <TextStyle variation="strong">Bet type</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item>
              <div style={{ width: '200px' }}>
                <TextStyle variation="strong">{homeTeamFirst ? game.home_team.name : game.away_team.name}</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item>
              <div style={{ width: '200px' }}>
                <TextStyle variation="strong">{homeTeamFirst ? game.away_team.name : game.home_team.name}</TextStyle>
              </div>
            </Stack.Item>
            <Stack.Item>
              <div style={{ width: '200px' }} />
            </Stack.Item>
            {/* <Stack.Item>
              <div style={{ width: '40px' }} />
            </Stack.Item> */}
          </Stack>
        </ResourceList.Item>
      </Card>
    );
  }

  renderItem(item, index) {
    const {
      game
    } = this.props;
    const homeTeamFirst = game && (game.between_players || (game.league && game.league.sport_category && game.league.sport_category.home_team_first === 1));
    let first;
    let second;
    let third;
    if (homeTeamFirst) {
      first = {
        [BET_TYPE.SPREAD]: 'weight_1',
        [BET_TYPE.MONEYLINE]: 'weight_1',
        [BET_TYPE.OVER_UNDER]: 'weight_1'
      };
      second = {
        [BET_TYPE.SPREAD]: 'weight_2',
        [BET_TYPE.MONEYLINE]: 'weight_2',
        [BET_TYPE.OVER_UNDER]: 'weight_2'
      };
      third = {
        [BET_TYPE.SPREAD]: 'weight_3',
        [BET_TYPE.MONEYLINE]: 'weight_3',
        [BET_TYPE.OVER_UNDER]: 'weight_3'
      };
    } else {
      first = {
        [BET_TYPE.SPREAD]: 'weight_2',
        [BET_TYPE.MONEYLINE]: 'weight_2',
        [BET_TYPE.OVER_UNDER]: 'weight_1'
      };
      second = {
        [BET_TYPE.SPREAD]: 'weight_1',
        [BET_TYPE.MONEYLINE]: 'weight_1',
        [BET_TYPE.OVER_UNDER]: 'weight_2'
      };
      third = {
        [BET_TYPE.SPREAD]: 'weight_3',
        [BET_TYPE.MONEYLINE]: 'weight_3',
        [BET_TYPE.OVER_UNDER]: 'weight_3'
      };
    }


    return (
      <div key={`${index}`}>
        <Formik
          ref={this.formikRef}
          initialValues={{
            id: item.id,
            weight_1: item.weight_1,
            weight_2: item.weight_2,
            weight_3: item.weight_3
          }}
          validationSchema={
            Yup.object().shape({
              weight_1: Yup.number().required('Weight 1 is required!'),
              weight_2: Yup.number(),
              weight_3: Yup.number()
            })
          }
          onSubmit={this.handleUpdateItem}
          render={({
            values,
            errors,
            touched,
            handleBlur,
            setFieldValue,
            handleSubmit
            // isSubmitting
          }) => (
            <Form onSubmit={handleSubmit}>
              <Card>
                <ResourceList.Item>
                  <Stack>
                    <Stack.Item>
                      <div style={{ width: '60px' }}>
                        <TextStyle variation="strong">{item.id}</TextStyle>
                      </div>
                    </Stack.Item>
                    <Stack.Item>
                      <div style={{ width: '120px' }}>
                        <TextStyle variation="strong">{item.bet_type.name}</TextStyle>
                      </div>
                    </Stack.Item>
                    <Stack.Item>
                      <div style={{ width: '200px' }}>
                        <TextField
                          name={first[item.bet_type.value]}
                          value={values[first[item.bet_type.value]]}
                          onBlur={handleBlur}
                          onChange={async (value) => {
                            if (item.bet_type.value === BET_TYPE.SPREAD) {
                              await setFieldValue(second[item.bet_type.value], -value);
                            }
                            await setFieldValue(first[item.bet_type.value], value);
                            await handleSubmit();
                          }}
                          error={touched[first[item.bet_type.value]] && errors[first[item.bet_type.value]]}
                        />
                      </div>
                    </Stack.Item>
                    <Stack.Item>
                      <div style={{ width: '200px' }}>
                        {
                          (item.bet_type.value === BET_TYPE.MONEYLINE || item.bet_type.value === BET_TYPE.SPREAD) && (
                            <TextField
                              name={second[item.bet_type.value]}
                              value={values[second[item.bet_type.value]]}
                              onBlur={handleBlur}
                              onChange={async (value) => {
                                if (item.bet_type.value === BET_TYPE.SPREAD) {
                                  await setFieldValue(first[item.bet_type.value], -value);
                                }
                                await setFieldValue(second[item.bet_type.value], value);
                                await handleSubmit();
                              }}
                              error={touched.weight_2 && errors.weight_2}
                            />
                          )
                        }
                      </div>
                    </Stack.Item>
                    <Stack.Item>
                      <div style={{ width: '200px' }}>
                        {
                          (item.bet_type.value === BET_TYPE.MONEYLINE) && (
                            <TextField
                              name={third[item.bet_type.value]}
                              value={values[third[item.bet_type.value]]}
                              onBlur={handleBlur}
                              onChange={async (value) => {
                                await setFieldValue(third[item.bet_type.value], value);
                                await handleSubmit();
                              }}
                              error={touched[third[item.bet_type.value]] && errors[third[item.bet_type.value]]}
                            />
                          )
                        }
                      </div>
                    </Stack.Item>
                    {/* <Stack.Item>
                      <div style={{ width: '40px' }}>
                        <Button
                          loading={isSubmitting}
                          submit
                          primary
                          icon="checkmark"
                        />
                      </div>
                    </Stack.Item> */}
                  </Stack>
                </ResourceList.Item>
              </Card>
            </Form>
          )}
        />
      </div>
    );
  }

  render() {
    const {
      data
    } = this.props;
    return (
      <Card
        title="Bet polls"
        sectioned
      >
        {
          <Fragment>
            {this.renderHeader()}
            {
              data.map((item, index) => (this.renderItem(item, index)))
            }
            {
              <div>
                <Card sectioned>
                  <Button
                    primary
                    onClick={this.handleCreate}
                  >
                    { (data && data.length > 0) ? 'Refresh Polls' : 'Create Polls' }
                  </Button>
                </Card>
              </div>
            }
          </Fragment>
        }
      </Card>
    );
  }
}

BetTypes.defaultProps = {
  game: {},
  data: [],
  onCreate: () => {},
  onUpdateItem: () => {}
};

export default BetTypes;
