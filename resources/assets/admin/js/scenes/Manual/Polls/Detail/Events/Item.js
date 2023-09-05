import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card, Stack, Autocomplete, Checkbox,
  Form, FormLayout, TextStyle, Icon,
  InlineError, TextField, Thumbnail,
  Button, ButtonGroup, ResourceList, SkeletonBodyText
} from '@shopify/polaris';
import Api from '../../../../../apis/app';
import OptionsHelper from '../../../../../helpers/OptionsHelper';

class Item extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isDeleting: false,
      isEditMode: false,
      showDeleteConfirm: false,
      candidates: [],
      options1: [],
      selectedOptions1: [],
      options2: [],
      selectedOptions2: []
    };
    this.formikRef = null;
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.toggleEditMode = this.toggleEditMode.bind(this);
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
  }

  async componentDidMount() {
    const {
      body: candidates
    } = await Api.get('admin/manual/candidates/all', {category_id: this.props.page.category.id});
    this.setState({
      candidates: OptionsHelper.getOptions(candidates, 'id', 'name')
    });
  }

  setDeleting(isDeleting) {
    this.setState({
      isDeleting
    });
  }

  toggleEditMode() {
    this.setState({
      isEditMode: !this.state.isEditMode
    });
  }

  toggleDeleteConfirm() {
    this.setState({
      showDeleteConfirm: !this.state.showDeleteConfirm
    });
  }

  async handleEdit(values, bags) {
    const {
      onEdit, data, index
    } = this.props;
    await onEdit(data.id, values, bags, this, index);
  }

  async handleDelete() {
    const {
      onDelete, data, index
    } = this.props;
    await onDelete(data.id, this, index);
  }

  renderEditMode() {
    const {
      data
    } = this.props;
    const {
      candidates, options1, selectedOptions1, options2, selectedOptions2
    } = this.state;
    return (
      <Formik
        ref={this.formikRef}
        initialValues={{
          name: data.name || '',
          name_es: data.name_es || '',
          candidate1_name: data.candidate1.name || '',
          candidate1_score: data.candidate1_score || 0,
          candidate1_standing: data.candidate1_standing || '',
          candidate1_odds: data.candidate1_odds || 0,
          candidate2_name: data.candidate2.name || '',
          candidate2_score: data.candidate2_score || 0,
          candidate2_standing: data.candidate2_standing || '',
          candidate2_odds: data.candidate2_odds || 0,
          candidate1_spread: data.spread || 0,
          candidate2_spread: -data.spread || 0,
          over_under: data.over_under || 0,
          over_under_score: data.over_under_score || 0,
          tie_odds: data.tie_odds || 0,
          spread_win_points: data.spread_win_points || 0,
          spread_loss_points: data.spread_loss_points || 0,
          moneyline1_win_points: data.moneyline1_win_points || 0,
          moneyline1_loss_points: data.moneyline1_loss_points || 0,
          moneyline2_win_points: data.moneyline2_win_points || 0,
          moneyline2_loss_points: data.moneyline2_loss_points || 0,
          moneyline_tie_win_points: data.moneyline_tie_win_points || 0,
          moneyline_tie_loss_points: data.moneyline_tie_loss_points || 0,
          over_under_win_points: data.over_under_win_points || 0,
          over_under_loss_points: data.over_under_loss_points || 0,
          published: data.published || 0,
        }}
        validationSchema={
          Yup.object().shape({
            name: Yup.string().required('Name is required!'),
            name_es: Yup.string().required('Name ES is required!'),
            candidate1_name: Yup.string().required('Candidate1 Name is required!'),
            candidate2_name: Yup.string().required('Candidate2 Name is required!'),
            candidate1_score: Yup.number(),
            candidate1_odds: Yup.number(),
            candidate2_score: Yup.number(),
            candidate2_odds: Yup.number(),
            candidate1_spread: Yup.number(),
            candidate2_spread: Yup.number(),
            over_under: Yup.number(),
            over_under_score: Yup.number(),
            tie_odds: Yup.number(),
            spread_win_points: Yup.number(),
            spread_loss_points: Yup.number(),
            moneyline1_win_points: Yup.number(),
            moneyline1_loss_points: Yup.number(),
            moneyline2_win_points: Yup.number(),
            moneyline2_loss_points: Yup.number(),
            moneyline_tie_win_points: Yup.number(),
            moneyline_tie_loss_points: Yup.number(),
            over_under_win_points: Yup.number(),
            over_under_loss_points: Yup.number(),
          })
        }
        onSubmit={this.handleEdit}
        render={({
          values,
          errors,
          status,
          touched,
          setFieldValue,
          handleSubmit,
          handleBlur,
          isSubmitting
        }) => (
          <Card>
            <ResourceList.Item>
              <Form onSubmit={handleSubmit}>
                <FormLayout>
                  <FormLayout.Group>
                    {status && <InlineError message={status} />}
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <Checkbox
                      label="Published"
                      checked={values.published == 1}
                      onChange={(value) => {
                        setFieldValue('published', value === true ? 1 : 0);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Name"
                      placeholder="Name"
                      name="name"
                      value={values.name}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('name', value);
                      }}
                      error={touched.name && errors.name}
                    />
                    <TextField
                      label="Name ES"
                      placeholder="Name ES"
                      name="name_es"
                      value={values.name_es}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('name_es', value);
                      }}
                      error={touched.name_es && errors.name_es}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <Autocomplete
                      options={options1}
                      selected={selectedOptions1}
                      onSelect={(selected) => {
                        const selectedValue = selected.map((selectedItem) => {
                          const matchedOption = options1.find((option) => {
                            return option.value.match(selectedItem);
                          });
                          return matchedOption && matchedOption.label;
                        });
                        this.setState({
                          selectedOptions1: selected
                        });
                        
                        setFieldValue('candidate1_name', selectedValue);
                      }}
                      textField={
                        <Autocomplete.TextField
                          onChange={(value) => {
                            setFieldValue('candidate1_name', value)

                            if (value === '') {
                              this.setState({
                                options1: candidates
                              });
                              return;
                            }

                            const filterRegex = new RegExp(value, 'i');
                            const resultOptions = candidates.filter((option) =>
                              option.label.match(filterRegex),
                            );
                            this.setState({
                              options1: resultOptions
                            })
                          }}
                          label="Candidate1 Name"
                          value={values.candidate1_name}
                          onBlur={handleBlur}
                          name="candidate1_name"
                          error={touched.candidate1_name && errors.candidate1_name}
                        />
                      }
                    />
                    <Autocomplete
                      options={options2}
                      selected={selectedOptions2}
                      onSelect={(selected) => {
                        const selectedValue = selected.map((selectedItem) => {
                          const matchedOption = options2.find((option) => {
                            return option.value.match(selectedItem);
                          });
                          return matchedOption && matchedOption.label;
                        });
                        this.setState({
                          selectedOptions2: selected
                        });
                        
                        setFieldValue('candidate2_name', selectedValue);
                      }}
                      textField={
                        <Autocomplete.TextField
                          onChange={(value) => {
                            setFieldValue('candidate2_name', value)

                            if (value === '') {
                              this.setState({
                                options2: candidates
                              });
                              return;
                            }

                            const filterRegex = new RegExp(value, 'i');
                            const resultOptions = candidates.filter((option) =>
                              option.label.match(filterRegex),
                            );
                            this.setState({
                              options2: resultOptions
                            })
                          }}
                          label="Candidate2 Name"
                          value={values.candidate2_name}
                          onBlur={handleBlur}
                          name="candidate2_name"
                          error={touched.candidate2_name && errors.candidate2_name}
                        />
                      }
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Candidate1 Score"
                      placeholder="Candidate1 Score"
                      name="candidate1_score"
                      value={values.candidate1_score}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('candidate1_score', value);
                      }}
                    />
                    <TextField
                      label="Candidate2 Score"
                      placeholder="Candidate2 Score"
                      name="candidate2_score"
                      value={values.candidate2_score}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('candidate2_score', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Candidate1 Standing"
                      placeholder="Candidate1 Standing"
                      name="candidate1_standing"
                      value={values.candidate1_standing}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('candidate1_standing', value);
                      }}
                    />
                    <TextField
                      label="Candidate2 Standing"
                      placeholder="Candidate2 Standing"
                      name="candidate2_standing"
                      value={values.candidate2_standing}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('candidate2_standing', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Candidate1 Odds"
                      placeholder="Candidate1 Odds"
                      name="candidate1_odds"
                      value={values.candidate1_odds}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('candidate1_odds', value);
                      }}
                    />
                    <TextField
                      label="Candidate2 Odds"
                      placeholder="Candidate2 Odds"
                      name="candidate2_odds"
                      value={values.candidate2_odds}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('candidate2_odds', value);
                      }}
                    />
                    <TextField
                      label="Tie Odds"
                      placeholder="Tie Odds"
                      name="tie_odds"
                      value={values.tie_odds}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('tie_odds', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Candidate1 Spread"
                      placeholder="Candidate1 Spread"
                      name="candidate1_spread"
                      value={values.candidate1_spread}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('candidate1_spread', value);
                        const value2 = parseFloat(value) || 0;
                        setFieldValue('candidate2_spread', -value2)
                      }}
                    />
                    <TextField
                      label="Candidate2 Spread"
                      placeholder="Candidate2 Spread"
                      name="candidate2_spread"
                      value={values.candidate2_spread}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('candidate2_spread', value);
                        const value2 = parseFloat(value) || 0;
                        setFieldValue('candidate1_spread', -value2)
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Over/Under"
                      placeholder="Over/Under"
                      name="over_under"
                      value={values.over_under}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('over_under', value);
                      }}
                    />
                    <TextField
                      label="Over/Under Score"
                      placeholder="Over/Under Score"
                      name="over_under_score"
                      value={values.over_under_score}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('over_under_score', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Spread Win Points"
                      placeholder="Spread Win Points"
                      name="spread_win_points"
                      value={values.spread_win_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('spread_win_points', value);
                      }}
                    />
                    <TextField
                      label="Spread Loss Points"
                      placeholder="Spread Loss Points"
                      name="spread_loss_points"
                      value={values.spread_loss_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('spread_loss_points', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Moneyline Candidate1 Win Points"
                      placeholder="Moneyline Candidate1 Win Points"
                      name="moneyline1_win_points"
                      value={values.moneyline1_win_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('moneyline1_win_points', value);
                      }}
                    />
                    <TextField
                      label="Moneyline Candidate1 Loss Points"
                      placeholder="Moneyline Candidate1 Loss Points"
                      name="moneyline1_loss_points"
                      value={values.moneyline1_loss_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('moneyline1_loss_points', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Moneyline Candidate2 Win Points"
                      placeholder="Moneyline Candidate2 Win Points"
                      name="moneyline2_win_points"
                      value={values.moneyline2_win_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('moneyline2_win_points', value);
                      }}
                    />
                    <TextField
                      label="Moneyline Candidate2 Loss Points"
                      placeholder="Moneyline Candidate2 Loss Points"
                      name="moneyline2_loss_points"
                      value={values.moneyline2_loss_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('moneyline2_loss_points', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Moneyline Tie Win Points"
                      placeholder="Moneyline Tie Win Points"
                      name="moneyline_tie_win_points"
                      value={values.moneyline_tie_win_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('moneyline_tie_win_points', value);
                      }}
                    />
                    <TextField
                      label="Moneyline Tie Loss Points"
                      placeholder="Moneyline Tie Loss Points"
                      name="moneyline_tie_loss_points"
                      value={values.moneyline_tie_loss_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('moneyline_tie_loss_points', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Over/Under Win Points"
                      placeholder="Over/Under Win Points"
                      name="over_under_win_points"
                      value={values.over_under_win_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('over_under_win_points', value);
                      }}
                    />
                    <TextField
                      label="Over/Under Loss Points"
                      placeholder="Over/Under Loss Points"
                      name="over_under_loss_points"
                      value={values.over_under_loss_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('over_under_loss_points', value);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <ButtonGroup segmented>
                      <Button
                        loading={isSubmitting}
                        submit
                        primary
                        icon="checkmark"
                      />
                      <Button
                        onClick={this.toggleEditMode}
                        icon="cancelSmall"
                      />
                    </ButtonGroup>
                  </FormLayout.Group>
                </FormLayout>
              </Form>
            </ResourceList.Item>
          </Card>
        )}
      />
    );
  }

  renderViewMode() {
    const {
      data
    } = this.props;
    const {
      showDeleteConfirm, isDeleting
    } = this.state;

    return (
      <Card>
        <ResourceList.Item>
          {
            showDeleteConfirm ? (
              <Stack>
                <Stack.Item fill>
                  {`Do you want to delete ${data.name}?`}
                </Stack.Item>
                <Stack.Item>
                  <ButtonGroup segmented>
                    <Button
                      primary
                      loading={isDeleting}
                      onClick={this.handleDelete}
                    >
                      Yes
                    </Button>
                    <Button
                      disabled={isDeleting}
                      onClick={this.toggleDeleteConfirm}
                    >
                      No
                    </Button>
                  </ButtonGroup>
                </Stack.Item>
              </Stack>
            ) : (
              <div {...this.props.dragHandleProps}>
                <Stack>
                  <Stack.Item>
                    <div style={{ width: '60px' }}>
                      {data.id}
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    {data.name}
                  </Stack.Item>
                  <Stack.Item fill>
                    {data.name_es}
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '100px' }}>
                      {data.published ? <Icon source='checkmark' /> : ''}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '80px' }}>
                      <ButtonGroup segmented>
                        <Button
                          primary
                          icon="products"
                          onClick={this.toggleEditMode}
                        />
                        <Button
                          destructive
                          icon="delete"
                          onClick={this.toggleDeleteConfirm}
                        />
                      </ButtonGroup>
                    </div>
                  </Stack.Item>
                </Stack>
              </div>
            )
          }
          <Card>
            <ResourceList.Item>
              <Stack>
                <Stack.Item>
                  <div style={{ width: '50px'}}>
                    <TextStyle variation="strong">Logo</TextStyle>
                  </div>
                </Stack.Item>
                <Stack.Item fill>
                  <div style={{ width: '120px'}}>
                    <TextStyle variation="strong">Name</TextStyle>
                  </div>
                </Stack.Item>
                <Stack.Item fill>
                  <div style={{ width: '80px'}}>
                    <TextStyle variation="strong">Abbr</TextStyle>
                  </div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '60px'}}>
                    <TextStyle variation="strong">Score</TextStyle>
                  </div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '100px'}}>
                    <TextStyle variation="strong">Standing</TextStyle>
                  </div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '60px'}}>
                    <TextStyle variation="strong">Odds</TextStyle>
                  </div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '60px'}}>
                    <TextStyle variation="strong">Spread</TextStyle>
                  </div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '60px'}}>
                    <TextStyle variation="strong">Over/Under</TextStyle>
                  </div>
                </Stack.Item>
              </Stack>
            </ResourceList.Item>
          </Card>
          <div>
            <Card>
              <ResourceList.Item>
                <Stack>
                  <Stack.Item>
                    <div style={{ width: '50px'}}>
                      <Thumbnail source={data.candidate1.logo} size="small" />
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <div style={{ width: '120px'}}>
                      {data.candidate1.name}
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <div style={{ width: '80px'}}>
                      {data.candidate1.short_name}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px'}}>
                      {data.candidate1_score}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '100px'}}>
                      {data.candidate1_standing}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px'}}>
                      {data.candidate1_odds}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px'}}>
                      {data.spread}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px'}}>
                      {data.over_under}
                    </div>
                  </Stack.Item>
                </Stack>
              </ResourceList.Item>
              <ResourceList.Item>
                <Stack>
                  <Stack.Item>
                    <div style={{ width: '50px'}}>
                      <Thumbnail source={data.candidate2.logo} size="small" />
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <div style={{ width: '120px'}}>
                      {data.candidate2.name}
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <div style={{ width: '80px'}}>
                      {data.candidate2.short_name}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px'}}>
                      {data.candidate2_score}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '100px'}}>
                      {data.candidate2_standing}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px'}}>
                      {data.candidate2_odds}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px'}}>
                      {-data.spread}
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px'}}>
                      {data.over_under_score}
                    </div>
                  </Stack.Item>
                </Stack>
              </ResourceList.Item>
            </Card>
          </div>
        </ResourceList.Item>
      </Card>
    );
  }

  render() {
    const {
      isLoading
    } = this.props;
    const {
      isEditMode
    } = this.state;
    if (isLoading) {
      return (
        <Card>
          <ResourceList.Item>
            <SkeletonBodyText />
          </ResourceList.Item>
        </Card>
      );
    }
    return isEditMode ? this.renderEditMode() : this.renderViewMode();
  }
}

Item.defaultProps = {
  isLoading: false,
  onEdit: () => {},
  onDelete: () => {}
  // onToggleActive: () => {}
};

export default Item;
